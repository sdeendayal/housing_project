@extends('layouts.mmgayBdoAuth')
@section('title', request()->has('all') ? 'All Eligible Beneficiaries' : 'Awaiting Visit Schedule')
@section('page_header', request()->has('all') ? 'Eligible List' : 'Awaiting Schedule')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex-1">


    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <!-- Search and Filter Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">event_note</span>
                    {{ request()->has('all') ? 'All Eligible Beneficiaries' : 'Awaiting Visit Schedule' }}
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5 font-semibold uppercase">{{ request()->has('all') ? 'List of all eligible MMGAY beneficiaries in block.' : 'List of eligible MMGAY beneficiaries awaiting visit scheduling.' }}</p>
            </div>
            <!-- Search bar - Denser -->
            <form action="{{ route('mmgay.bdo.eligibility-list') }}" method="GET" class="flex gap-2">
                @if(request()->has('all'))
                    <input type="hidden" name="all" value="1">
                @endif
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, mobile, reg..." class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 w-56">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] font-bold">search</span> Filter
                </button>
                @if($search)
                    <a href="{{ route('mmgay.bdo.eligibility-list', request()->has('all') ? ['all' => 1] : []) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table - High Density spacing -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                        <th class="px-3 py-2 text-left">Sr.No.</th>
                        <th class="px-3 py-2 text-left">App Number / Reg</th>
                        <th class="px-3 py-2 text-left">Applicant Name</th>
                        <th class="px-3 py-2 text-left">Father's Name</th>
                        <th class="px-3 py-2 text-left">Mobile No</th>
                        <th class="px-3 py-2 text-left">Block</th>
                        <th class="px-3 py-2 text-left">District</th>
                        <th class="px-3 py-2 text-left">Possession Status</th>
                        <th class="px-3 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-3 py-1.5 font-bold text-slate-400">{{ $loop->iteration + ($applications->currentPage() - 1) * $applications->perPage() }}</td>
                            <td class="px-3 py-1.5 font-bold text-slate-800">{{ $app->application_number ?? 'Awaiting Init' }}</td>
                            <td class="px-3 py-1.5 text-slate-700 font-medium">{{ $app->applicant_name }}</td>
                            <td class="px-3 py-1.5 text-slate-500">{{ $app->father_name ?? '—' }}</td>
                            <td class="px-3 py-1.5 font-mono text-slate-500 text-[11px]">{{ $app->mobile }}</td>
                            <td class="px-3 py-1.5 text-slate-500 text-[11px]">{{ $app->block_name ?? '—' }}</td>
                            <td class="px-3 py-1.5 text-slate-500 text-[11px]">{{ $app->district_name }}</td>
                            <td class="px-3 py-1.5">
                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase
                                    @if($app->physical_possession_status === 'Visit Scheduled') bg-orange-50 text-orange-700 border border-orange-100
                                    @else bg-blue-50 text-blue-700 border border-blue-100
                                    @endif">
                                    {{ $app->physical_possession_status }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-center font-semibold">
                                @if($app->physical_possession_status === 'Eligible for Physical Possession')
                                    <a href="{{ route('mmgay.bdo.schedule-form', $app->secure_id) }}" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-2.5 py-1 rounded-md font-extrabold transition shadow-sm font-bold">
                                        <span class="material-symbols-outlined text-[13px] font-bold">calendar_month</span> Schedule Visit
                                    </a>
                                @else
                                    <a href="{{ route('mmgay.bdo.schedule-form', $app->secure_id) }}" class="inline-flex items-center gap-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-[10px] px-2.5 py-1 rounded-md font-extrabold transition shadow-sm font-bold">
                                        <span class="material-symbols-outlined text-[13px] font-bold">edit_calendar</span> Reschedule Visit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-slate-400 font-semibold">No awaiting applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination - Compact -->
        <div class="mt-4">
            {{ $applications->links('pagination::tailwind') }}
        </div>
    </div>
</main>
@endsection
