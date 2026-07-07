@extends('layouts.mmgayAdmin')

@section('title', 'Assigned Flats Listing')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <!-- PAGE CARD -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Assigned Flats Information Listing</h2>
                    <p class="text-sm text-gray-500 mt-1">Overview of all systematically assigned and paid units</p>
                </div>

                <div
                    class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold text-sm shadow-sm flex items-center gap-1">
                    Total Assigned: {{ $totalAssigned }}
                </div>
            </div>

            <!-- SEARCH / FILTER BAR -->
            <div class="p-5 bg-slate-50 border-b">
                <form action="{{ route('superadmin.assigned.flats') }}" method="GET" class="flex gap-4 items-center">
                    <div class="relative flex-1">
                        <span
                            class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input type="text" name="search"
                            class="w-full bg-white border border-slate-200 rounded-xl pl-11 pr-4 py-2.5 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-all"
                            placeholder="Search by Owner, Mobile, Registration No, Village or Flat No..."
                            value="{{ $search }}">
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-6 py-2.5 rounded-xl transition shadow-sm">
                            Filter
                        </button>
                        @if (!empty($search))
                            <a href="{{ route('superadmin.assigned.flats') }}"
                                class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-sm px-5 py-2.5 rounded-xl transition text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <!-- HEADER -->
                    <thead class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-3 text-left w-16">#</th>
                            <th class="p-3 text-left">Flat Identity</th>
                            <th class="p-3 text-left">Beneficiary Details</th>
                            <th class="p-3 text-left">Contact & Registry</th>
                            <th class="p-3 text-left">Location Context</th>
                            <th class="p-3 text-center">Verification</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($assignedFlats as $index => $flat)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <!-- Index -->
                                <td class="p-3 font-medium text-gray-400">
                                    {{ $assignedFlats->firstItem() + $index }}
                                </td>

                                <!-- Flat Identity -->
                                <td class="p-3">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 font-semibold text-xs tracking-wide border border-indigo-100/60">
                                        <span class="material-symbols-outlined text-[15px]">apartment</span>
                                        {{ $flat->FlatNo }}
                                    </div>
                                </td>

                                <!-- Beneficiary Details -->
                                <td class="p-3">
                                    <div class="font-bold text-gray-700 uppercase tracking-wide">{{ $flat->OwnerName }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-medium uppercase mt-0.5">
                                        {{ $flat->FatherHusbandName }}</div>
                                </td>

                                <!-- Contact & Registry -->
                                <td class="p-3">
                                    <div class="text-gray-600 font-medium flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-gray-400 text-[16px]">call</span>
                                        {{ $flat->MobileNo }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1 font-mono">Reg: {{ $flat->RegistrationNo }}</div>
                                </td>

                                <!-- Location Context -->
                                <td class="p-3">
                                    <div class="text-gray-700 font-semibold">{{ $flat->VillageName }}</div>
                                    <div class="text-xs text-gray-400 mt-1 flex items-center gap-2">
                                        <span class="flex items-center gap-0.5">
                                            <span
                                                class="material-symbols-outlined text-[13px] text-gray-400">location_on</span>
                                            {{ $flat->DistrictName }}
                                        </span>
                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                        <span class="text-indigo-600 font-semibold">Phase {{ $flat->Phase }}</span>
                                    </div>
                                </td>

                                <!-- Verification Status -->
                                <td class="p-3 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Paid & Assigned
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-12 text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-5xl mb-2">grid_off</span>
                                        <p class="text-base font-semibold text-slate-700">No matching flats found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- PAGINATION -->
            @if ($assignedFlats->hasPages())
                <div class="px-6 py-4 border-t bg-slate-50">
                    {{ $assignedFlats->links('pagination::tailwind') }}
                </div>
            @endif

        </div>
    </main>

@endsection
