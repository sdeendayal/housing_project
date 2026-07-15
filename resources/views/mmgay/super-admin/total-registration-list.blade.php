<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

@extends('layouts.mmgayAdmin')

@section('title', 'Total Registration List')

@section('content')

    <main class="min-h-screen bg-slate-50 p-6 pt-24 ml-[260px]">

        <!-- Header -->

        <div
            class="bg-white rounded-2xl shadow border border-slate-200 p-5 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">

            <div>
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-signature text-blue-600"></i>
                    Total Registration List
                </h2>

                <p class="text-slate-500 text-xs mt-1">
                    Registry Records
                </p>
            </div>

            <form method="GET">

                <div class="flex items-center gap-2">

                    <div class="relative">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search Registry..."
                            class="border border-slate-300 rounded-lg pl-10 pr-4 py-2 text-sm w-72 focus:ring-2 focus:ring-blue-500 focus:outline-none">

                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    </div>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">

                        <i class="fa-solid fa-search mr-1"></i>
                        Search

                    </button>

                    @if (request('search'))
                        <a href="{{ route('superadmin.total.registration') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg text-sm transition">
                            <i class="fa-solid fa-rotate-left mr-1"></i>
                            Reset
                        </a>
                    @endif

                </div>

            </form>

        </div>

        <!-- Table -->

        <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full text-xs">

                    <thead class="bg-gradient-to-r from-slate-700 to-slate-800 text-white">

                        <tr>

                            <th class="px-3 py-3">#</th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-hashtag mr-1"></i>
                                Registry
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-calendar mr-1"></i>
                                Date
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-location-dot mr-1"></i>
                                District
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-map mr-1"></i>
                                Tehsil
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-house mr-1"></i>
                                Village
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-user mr-1"></i>
                                First Party
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-users mr-1"></i>
                                Second Party
                            </th>

                            <th class="px-3 py-3">
                                <i class="fa-solid fa-phone mr-1"></i>
                                Mobile
                            </th>

                            <th class="px-3 py-3 text-center">
                                <i class="fa-solid fa-ruler mr-1"></i>
                                Area
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse($registrations as $row)
                            <tr class="hover:bg-blue-50 transition duration-200">

                                <td class="px-3 py-2 font-semibold">
                                    {{ $loop->iteration + $registrations->firstItem() - 1 }}
                                </td>

                                <td class="px-3 py-2 font-semibold text-blue-700">
                                    {{ $row->RegistaryNumber }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($row->RegistaryDate)->format('d-m-Y') }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $row->District }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $row->TehsilName }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ $row->Village }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ Str::limit($row->FirstParty, 30) }}
                                </td>

                                <td class="px-3 py-2 font-medium">
                                    {{ Str::limit($row->SecondParty, 30) }}
                                </td>

                                <td class="px-3 py-2">

                                    <a href="tel:{{ $row->SecondPartyMobile }}" class="text-blue-600 hover:underline">

                                        <i class="fa-solid fa-phone-volume mr-1"></i>

                                        {{ $row->SecondPartyMobile }}

                                    </a>

                                </td>

                                <td class="px-3 py-2 text-center">

                                    <span
                                        class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-[11px] font-semibold">

                                        {{ $row->TransferArea }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="text-center py-10 text-gray-500">

                                    <i class="fa-solid fa-folder-open text-3xl mb-2"></i>

                                    <br>

                                    No Registration Found

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Pagination -->

        <div
            class="bg-white rounded-2xl shadow border border-slate-200 mt-5 px-5 py-4 flex flex-col md:flex-row justify-between items-center gap-4">

            <div class="text-xs text-gray-600">



            </div>

            <div class="modern-pagination">

                {{ $registrations->withQueryString()->links('pagination::tailwind') }}

            </div>

        </div>

    </main>

@endsection
