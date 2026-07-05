@extends('layouts.mmgayBdoAuth')
@section('title', 'Possession Applications')
@section('page_header', 'Applications List')

@section('content')
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex-1">


    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <!-- Search and Filter Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">list_alt</span>
                    Possession Applications
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5 font-semibold uppercase">Manage all scheduled, selected, verified and rejected applications.</p>
            </div>
            <!-- Search & Status filters - Compact -->
            <form action="{{ route('mmgay.bdo.possession-applications') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="status" class="border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 w-36">
                    <option value="">All Statuses</option>
                    <option value="Visit Scheduled" {{ $status === 'Visit Scheduled' ? 'selected' : '' }}>Visit Scheduled</option>
                    <option value="Slot Selected" {{ $status === 'Slot Selected' ? 'selected' : '' }}>Slot Selected</option>
                    <option value="Verified" {{ $status === 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Rejected" {{ $status === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name, mobile, reg..." class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 w-44">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] font-bold">search</span> Filter
                </button>
                @if($search || $status)
                    <a href="{{ route('mmgay.bdo.possession-applications') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table - High Density spacing -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                        <th class="px-3 py-2 text-left">Sr.No.</th>
                        <th class="px-3 py-2 text-left">App Number</th>
                        <th class="px-3 py-2 text-left">Applicant Name</th>
                        <th class="px-3 py-2 text-left">Mobile No</th>
                        <th class="px-3 py-2 text-left">Scheduled visit date</th>
                        <th class="px-3 py-2 text-left">Confirmed date</th>
                        <th class="px-3 py-2 class text-left">Status</th>
                        <th class="px-3 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-3 py-1.5 font-bold text-slate-400">{{ $loop->iteration + ($applications->currentPage() - 1) * $applications->perPage() }}</td>
                            <td class="px-3 py-1.5 font-bold text-slate-800">{{ $app->application_number }}</td>
                            <td class="px-3 py-1.5 text-slate-700 font-medium">{{ $app->applicant_name }}</td>
                            <td class="px-3 py-1.5 font-mono text-slate-500 text-[11px]">{{ $app->mobile }}</td>
                            <td class="px-3 py-1.5 text-slate-500 text-[11px]">
                                @if($app->visit_slot_1)
                                    {{ Carbon\Carbon::parse($app->visit_slot_1)->format('d M Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-1.5 text-slate-500 text-[11px]">
                                @if($app->citizen_visit_date && $app->physical_possession_status === 'Slot Selected')
                                    <span class="font-bold text-indigo-600">{{ Carbon\Carbon::parse($app->citizen_visit_date)->format('d M Y, h:i A') }}</span>
                                @elseif($app->physical_possession_status === 'Verified')
                                    <span class="text-emerald-600 font-medium">{{ $app->possession_date ? Carbon\Carbon::parse($app->possession_date)->format('d M Y') : 'Completed' }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-1.5">
                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase
                                    @if($app->physical_possession_status === 'Verified') bg-emerald-50 text-emerald-700 border border-emerald-100
                                    @elseif($app->physical_possession_status === 'Visit Scheduled') bg-orange-50 text-orange-700 border border-orange-100
                                    @elseif($app->physical_possession_status === 'Slot Selected') bg-indigo-50 text-indigo-700 border border-indigo-100
                                    @elseif($app->physical_possession_status === 'Site Verified') bg-blue-50 text-blue-700 border border-blue-100
                                    @elseif($app->physical_possession_status === 'Rejected') bg-rose-50 text-rose-700 border border-rose-100
                                    @else bg-slate-50 text-slate-700 border border-slate-100
                                    @endif">
                                    {{ $app->physical_possession_status }}
                                </span>
                            </td>
                            <td class="px-3 py-1.5 text-center">
                                @if(in_array($app->physical_possession_status, ['Slot Selected', 'Site Verified', 'Visit Scheduled', 'Rejected']))
                                    <a href="{{ route('mmgay.bdo.verify-form', $app->secure_id) }}" class="inline-flex items-center gap-1 bg-[#10b981] hover:bg-[#059669] text-white text-[10px] px-2.5 py-1 rounded-md font-extrabold transition">
                                        <span class="material-symbols-outlined text-[13px] font-bold">assignment_turned_in</span> Action / Verify
                                    </a>
                                @elseif($app->physical_possession_status === 'Verified')
                                    <a href="{{ route('mmgay.bdo.download-certificate', $app->secure_id) }}?inline=1" target="_blank" class="inline-flex items-center gap-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] px-2.5 py-1 rounded-md font-extrabold border border-slate-200 transition">
                                        <span class="material-symbols-outlined text-[13px] font-bold">picture_as_pdf</span> View PDF
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-slate-400 font-semibold">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $applications->links('pagination::tailwind') }}
        </div>
    </div>
</main>
@endsection
