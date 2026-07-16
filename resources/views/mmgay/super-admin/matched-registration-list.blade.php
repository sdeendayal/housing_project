<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

@extends('layouts.mmgayAdmin')

@section('title', 'Matched Registration List')

@section('content')

    <main class="min-h-screen bg-slate-50 p-6 pt-24 ml-[260px]">

        <!-- Header -->

        <div
            class="bg-white rounded-2xl shadow border border-slate-200 p-5 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">

            <div>
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    Matched Registration List
                </h2>

                <p class="text-slate-500 text-xs mt-1">
                    Registry Records Matched with Owner Master
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

        <div class="bg-white rounded-2xl shadow border border-slate-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full text-xs">

                    <thead class="bg-slate-100 text-slate-700 uppercase">

                        <tr>

                            <th class="px-3 py-3 text-center w-12">#</th>

                            <th class="px-3 py-3 text-left">
                                <i class="fa-solid fa-file-lines text-green-600 mr-1"></i>
                                Registry No
                            </th>

                            <th class="px-3 py-3 text-left">
                                <i class="fa-solid fa-calendar-days text-blue-600 mr-1"></i>
                                Date
                            </th>

                            <th class="px-3 py-3 text-left">
                                <i class="fa-solid fa-location-dot text-red-500 mr-1"></i>
                                District
                            </th>

                            <th class="px-3 py-3 text-left">
                                Tehsil
                            </th>

                            <th class="px-3 py-3 text-left">
                                Village
                            </th>

                            <th class="px-3 py-3 text-left">
                                First Party
                            </th>

                            <th class="px-3 py-3 text-left">
                                Second Party
                            </th>

                            <th class="px-3 py-3 text-center">
                                <i class="fa-solid fa-phone text-emerald-600 mr-1"></i>
                                Mobile
                            </th>

                            <th class="px-3 py-3 text-right">
                                Transfer Area
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($registrations as $row)
                            <tr class="border-t hover:bg-slate-50 transition">

                                <td class="px-3 py-2 text-center font-medium">
                                    {{ $registrations->firstItem() + $loop->index }}
                                </td>

                                <td class="px-3 py-2 font-semibold text-blue-700">
                                    {{ $row->RegistaryNumber }}
                                </td>

                                <td class="px-3 py-2">
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

                                <td class="px-3 py-2 max-w-xs">
                                    {{ $row->FirstParty }}
                                </td>

                                <td class="px-3 py-2 max-w-xs font-medium">
                                    {{ $row->SecondParty }}
                                </td>

                                <td class="px-3 py-2 text-center">
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[11px]">
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

                                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>

                                    <br>

                                    No Matched Registration Found

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
