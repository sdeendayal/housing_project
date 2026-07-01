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
            <div class="overflow-hidden">

                <table class="w-full table-fixed text-sm">

                    <table class="min-w-full text-sm whitespace-nowrap">

                        <thead class="bg-gray-100 border-b text-gray-600 text-xs uppercase">
                            <tr>
                                <th class="w-16 px-3 py-3 text-left">Action</th>
                                <th class="w-52 px-3 py-3 text-left">Location</th>
                                <th class="w-40 px-3 py-3 text-left">Category</th>
                                <th class="w-44 px-3 py-3 text-left">Registration No</th>
                                <th class="w-56 px-3 py-3 text-left">Owner Details</th>
                                <th class="w-48 px-3 py-3 text-left">Father/Husband</th>
                                <th class="w-32 px-3 py-3 text-center">Status</th>
                                <th class="w-32 px-3 py-3 text-center">Payment</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($owners as $owner)
                                <tr class="hover:bg-gray-50 transition">

                                    <!-- Action -->
                                    <td class="px-3 py-3 align-top">
                                        <a href="{{ route('owner.view', $owner->OwnerId) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 hover:bg-blue-100 transition">
                                            <span class="material-symbols-outlined text-[18px]">
                                                visibility
                                            </span>
                                        </a>
                                    </td>

                                    <!-- District + Village -->
                                    <td class="px-3 py-3 align-top">
                                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[18px] text-blue-600">
                                                location_city
                                            </span>
                                            {{ $owner->DistrictName ?? '-' }}
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1 ml-7 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">
                                                home_pin
                                            </span>
                                            {{ $owner->VillageName ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td class="px-3 py-3 align-top">
                                        <div class="flex flex-col">

                                            <div class="flex items-center gap-2 font-semibold text-gray-900">
                                                <span class="material-symbols-outlined text-[18px] text-purple-600">
                                                    category
                                                </span>

                                                {{ $owner->Caste ?? '-' }}
                                            </div>

                                        </div>
                                    </td>

                                    <!-- Registration -->
                                    <td class="px-3 py-3 align-top">
                                        <div class="flex flex-col">

                                            <div class="flex items-center gap-2 font-semibold text-gray-900">
                                                <span class="material-symbols-outlined text-[18px] text-indigo-600">
                                                    badge
                                                </span>

                                                {{ $owner->RegistrationNo ?? '-' }}
                                            </div>



                                        </div>
                                    </td>

                                    <!-- Owner + Mobile -->
                                    <td class="px-3 py-3 align-top">

                                        <div class="font-semibold text-gray-900 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[18px] text-blue-600">
                                                person
                                            </span>

                                            {{ $owner->OwnerName }}
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1 ml-7 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">
                                                call
                                            </span>

                                            {{ $owner->MobileNo }}
                                        </div>

                                    </td>

                                    <!-- Father/Husband -->
                                    <td class="px-3 py-3 max-w-[180px] break-words">
                                        {{ $owner->FatherHusbandName ?? '-' }}
                                    </td>

                                    <!-- Approved -->
                                    <td class="px-4 py-4 text-center">

                                        @if ($owner->IsPaid == 1)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">payments</span>
                                                Paid
                                            </span>
                                        @elseif ($owner->IsApproved == 1)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">verified</span>
                                                Approved
                                            </span>
                                        @elseif ($owner->IsRejected == 1)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">cancel</span>
                                                Rejected
                                            </span>
                                        @elseif ($owner->IsDcReconsidered == 1)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">hourglass_top</span>
                                                In Process
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                                Pending
                                            </span>
                                        @endif

                                    </td>

                                    <!-- Payment -->
                                    <td class="px-4 py-4 text-center">

                                        @if ($owner->IsPaid)
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">

                                                <span class="material-symbols-outlined text-[16px]">
                                                    payments
                                                </span>

                                                Paid

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">

                                                <span class="material-symbols-outlined text-[16px]">
                                                    pending_actions
                                                </span>

                                                Unpaid

                                            </span>
                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="py-10 text-center text-gray-500">
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
