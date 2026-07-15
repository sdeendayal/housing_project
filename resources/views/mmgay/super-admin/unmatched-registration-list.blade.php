@extends('layouts.mmgayAdmin')

@section('title', 'Unmatched Registration List')

@section('content')

    <main class="min-h-screen bg-slate-50 p-6 pt-24 ml-[260px]">

        <!-- Header -->

        <div
            class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">

            <div>
                <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-red-600"></i>
                    Unmatched Registration List
                </h2>

                <p class="text-slate-500 text-sm mt-1">
                    Registry Records Not Matched with Owner Master
                </p>
            </div>

            <form method="GET">
                <div class="flex items-center gap-2">

                    <div class="relative">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by Mobile, Registry No, Village..."
                            class="w-80 pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">

                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    </div>

                    <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">

                        <i class="fa-solid fa-search mr-1"></i>
                        Search

                    </button>

                    @if (request()->filled('search'))
                        <a href="{{ url()->current() }}"
                            class="px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-medium transition">

                            <i class="fa-solid fa-rotate-left mr-1"></i>
                            Reset

                        </a>
                    @endif

                </div>
            </form>

        </div>

        <!-- Table -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full text-xs">

                    <thead class="bg-slate-100 text-slate-700 uppercase">

                        <tr>

                            <th class="px-3 py-3 text-center">#</th>
                            <th class="px-3 py-3 text-left">
                                <i class="fa-solid fa-file-lines text-red-600 mr-1"></i>
                                Registry No
                            </th>
                            <th class="px-3 py-3 text-left">
                                <i class="fa-solid fa-calendar-days text-blue-600 mr-1"></i>
                                Date
                            </th>
                            <th class="px-3 py-3 text-left">District</th>
                            <th class="px-3 py-3 text-left">Tehsil</th>
                            <th class="px-3 py-3 text-left">Village</th>
                            <th class="px-3 py-3 text-left">First Party</th>
                            <th class="px-3 py-3 text-left">Second Party</th>
                            <th class="px-3 py-3 text-center">
                                <i class="fa-solid fa-phone text-red-600 mr-1"></i>
                                Mobile
                            </th>
                            <th class="px-3 py-3 text-right">Transfer Area</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($registrations as $row)
                            <tr class="border-t hover:bg-red-50 transition">

                                <td class="px-3 py-2 text-center">
                                    {{ $registrations->firstItem() + $loop->index }}
                                </td>

                                <td class="px-3 py-2 font-semibold text-red-700">
                                    {{ $row->RegistaryNumber }}
                                </td>

                                <td class="px-3 py-2">
                                    {{ \Carbon\Carbon::parse($row->RegistaryDate)->format('d-m-Y') }}
                                </td>

                                <td class="px-3 py-2">{{ $row->District }}</td>

                                <td class="px-3 py-2">{{ $row->TehsilName }}</td>

                                <td class="px-3 py-2">{{ $row->Village }}</td>

                                <td class="px-3 py-2">{{ $row->FirstParty }}</td>

                                <td class="px-3 py-2">{{ $row->SecondParty }}</td>

                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[11px]">
                                        {{ $row->SecondPartyMobile }}
                                    </span>
                                </td>

                                <td class="px-3 py-2 text-right font-semibold">
                                    {{ number_format($row->TransferArea, 2) }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="py-12 text-center text-gray-500">

                                    <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3"></i>

                                    <br>

                                    No Unmatched Registration Found

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Pagination -->

        <div
            class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mt-5 flex flex-col md:flex-row justify-between items-center">

            <div class="text-xs text-slate-600 mb-3 md:mb-0">



            </div>

            {{ $registrations->withQueryString()->links('pagination::tailwind') }}

        </div>

    </main>

@endsection
