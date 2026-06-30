@extends('layouts.mmgayCEOAuth')
@section('title', 'MMGAY - List')

@section('content')

    <main class="ml-[260px] mt-16 p-6 bg-gray-100 min-h-screen">

        <div class="bg-white rounded-xl shadow">

            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">
                    Phase {{ $phase }} - {{ ucfirst($status) }} List
                </h2>

                <a href="{{ route('district.dashboard') }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    ← Back Dashboard
                </a>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-sm whitespace-nowrap">

                        <thead class="bg-gray-100 border-b text-gray-600 text-xs uppercase">

                            <tr>

                                <th class="px-3 py-3 text-left">Actions</th>

                                <th class="px-3 py-3 text-left">Phase</th>

                                <th class="px-3 py-3 text-left">District</th>

                                <th class="px-3 py-3 text-left">Block</th>

                                <th class="px-3 py-3 text-left">Village</th>

                                <th class="px-3 py-3 text-left">Category</th>

                                <th class="px-3 py-3 text-left">Registration No</th>

                                <th class="px-3 py-3 text-left">Owner</th>

                                <th class="px-3 py-3 text-left">Father/Husband Name</th>

                                <th class="px-3 py-3 text-left">Mobile No</th>

                                <th class="px-3 py-3 text-left">Asset Number</th>

                                <th class="px-3 py-3 text-left">Remarks</th>

                                <th class="px-3 py-3 text-center">Approved</th>

                                <th class="px-3 py-3 text-center">Payment Status</th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($owners as $owner)
                                <tr class="hover:bg-gray-50">

                                    <td class="px-3 py-3">

                                        <a href="#"
                                            class="w-8 h-8 rounded bg-gray-100 hover:bg-blue-100 flex items-center justify-center">

                                            <span class="material-symbols-outlined text-[18px]">
                                                visibility
                                            </span>

                                        </a>

                                    </td>

                                    <td class="px-3 py-3">
                                        Phase-{{ $owner->Phase }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->DistrictName }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->BlockName }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->VillageName }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->Caste ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 font-medium text-blue-700">
                                        {{ $owner->RegistrationNo }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->OwnerName }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->FatherHusbandName }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->MobileNo }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->FlatNo }}
                                    </td>

                                    <td class="px-3 py-3">
                                        {{ $owner->Remarks ?? '-' }}
                                    </td>

                                    <td class="px-3 py-3 text-center">

                                        @if ($owner->IsApproved)
                                            <span
                                                class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                                Approved
                                            </span>
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif

                                    </td>

                                    <td class="px-3 py-3 text-center">

                                        @if ($owner->IsPaid)
                                            <span
                                                class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                                Paid
                                            </span>
                                        @else
                                            <span class="text-gray-500">—</span>
                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="14" class="py-10 text-center text-gray-500">

                                        No Record Found

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- Pagination -->
            <!-- Pagination -->
            <div class="p-5">
                {{ $owners->onEachSide(1)->links('pagination::tailwind') }}
            </div>

        </div>

    </main>

@endsection
