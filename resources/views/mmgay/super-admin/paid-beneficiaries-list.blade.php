@extends('layouts.mmgayAdmin')

@section('title', 'Paid Beneficiaries List - Super Admin')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="flex items-center justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Paid Beneficiaries List</h2>
                    <p class="text-sm text-gray-500 mt-1">All verified payments from registered applicants</p>
                </div>

                <div class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 font-semibold text-sm shadow-sm flex items-center gap-1">
                    Total Paid: {{ $totalPaid }}
                </div>
            </div>

            <div class="p-5 bg-slate-50 border-b">
                <form action="{{ route('superadmin.paid.beneficiaries') }}" method="GET" class="flex gap-4 items-center">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                        <input 
                            type="text" 
                            name="search" 
                            class="w-full bg-white border border-slate-200 rounded-xl pl-11 pr-4 py-2.5 text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-all"
                            placeholder="Search by Name, Mobile, Registration No, Village or District..." 
                            value="{{ $search }}"
                        >
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-6 py-2.5 rounded-xl transition shadow-sm">
                            Filter
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('superadmin.paid.beneficiaries') }}" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-medium text-sm px-5 py-2.5 rounded-xl transition text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-3 text-left w-16">#</th>
                            <th class="p-3 text-left">Registration No</th>
                            <th class="p-3 text-left">Beneficiary Name</th>
                            <th class="p-3 text-left">Contact Info</th>
                            <th class="p-3 text-left">Demographics Area</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($paidBeneficiaries as $index => $beneficiary)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-3 font-medium text-gray-400">
                                    {{ $paidBeneficiaries->firstItem() + $index }}
                                </td>
                                
                                <td class="p-3 text-indigo-600 font-semibold tracking-wide">
                                    {{ $beneficiary->RegistrationNo }}
                                </td>
                                        
                                <td class="p-3">
                                    <div class="font-bold text-gray-700 uppercase tracking-wide">{{ $beneficiary->OwnerName }}</div>
                                    <div class="text-xs text-gray-400 font-medium uppercase mt-0.5">{{ $beneficiary->FatherHusbandName }}</div>
                                </td>
                                
                                <td class="p-3">
                                    <div class="text-gray-600 font-medium flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-gray-400 text-[16px]">call</span>
                                        {{ $beneficiary->MobileNo }}
                                    </div>
                                </td>
                                
                                <td class="p-3">
                                    <div class="text-gray-700 font-semibold">{{ $beneficiary->VillageName }}</div>
                                    <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px] text-gray-400">location_on</span>
                                        {{ $beneficiary->DistrictName }}
                                    </div>
                                </td>
                                
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Paid
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-12 text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-5xl mb-2">grid_off</span>
                                        <p class="text-base font-semibold text-slate-700">No paid beneficiaries records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if($paidBeneficiaries->hasPages())
                <div class="px-6 py-4 border-t bg-slate-50">
                    {{ $paidBeneficiaries->links('pagination::tailwind') }}
                </div>
            @endif

        </div>
    </main>

@endsection