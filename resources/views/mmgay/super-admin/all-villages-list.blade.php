@extends('layouts.mmgayAdmin')

@section('title', 'All Villages Report')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <!-- PAGE CARD -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">All Villages Master List</h2>
                    <p class="text-sm text-gray-500 mt-1">Detailed phase and payment matrix for all valid villages</p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold text-sm shadow-sm self-start sm:self-center">
                    Total Villages: {{ $villagesData->total() }}
                </div>
            </div>

            <!-- DYNAMIC FILTERS BAR -->
            <div class="p-4 bg-slate-50 border-b border-gray-200">
                <form action="{{ url()->current() }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">

                    <!-- Search Field -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search Village</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Type village name..."
                            class="w-full px-4 py-2 border rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                    </div>

                    <!-- District Filter Dropdown -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filter by District</label>
                        <select name="district_id"
                            class="w-full px-4 py-2 border rounded-xl bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-gray-300">
                            <option value="">-- All Districts --</option>
                            @foreach ($districtsList as $district)
                                <option value="{{ $district->DistrictId }}"
                                    {{ $districtFilter == $district->DistrictId ? 'selected' : '' }}>
                                    {{ $district->DistrictName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="w-full md:w-1/3 flex items-end gap-2 pt-5">
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition shadow-sm w-full md:w-auto">
                            Apply
                        </button>
                        <a href="{{ url()->current() }}"
                            class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl text-sm transition text-center w-full md:w-auto">
                            Clear
                        </a>
                    </div>

                </form>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <!-- HEADERS -->
                    <thead class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-3 text-left">Village</th>
                            <th class="p-3 text-center">Beneficiaries</th>
                            <th class="p-3 text-center">Allotment</th>
                            <th class="p-3 text-center">Assigned Flats</th>
                            <th class="p-3 text-center">Paid</th>
                            <th class="p-3 text-center">Not Paid</th> 
                            <th class="p-3 text-center">Gap</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($villagesData as $v)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <!-- 1. Village Name -->
                                <td class="p-3 font-bold text-gray-800">
                                    {{ $v->VillageName }}
                                </td>
                                <!-- 2. Beneficiaries (Clickable link with automatic query values passing) -->
                                <td class="p-3 text-center">
                                    <a href="{{ route('superadmin.beneficiaries.index', ['district_id' => $v->DistrictId, 'village_id' => $v->VillageId]) }}" 
                                       class="text-blue-600 font-bold hover:text-blue-800 hover:underline transition-colors">
                                        {{ $v->Beneficiaries }}
                                    </a>
                                </td>
                                <!-- 3. Allotment -->
                                <td class="p-3 text-center text-gray-600">{{ $v->Allotment }}</td>
                                <!-- 4. Assigned Flats -->
                                <td class="p-3 text-center text-gray-600">{{ $v->AssignedFlats }}</td>
                                <!-- 5. Paid -->
                                <td class="p-3 text-center text-green-600 font-semibold">{{ $v->Paid }}</td>
                                <!-- 6. Not Paid -->
                                <td class="p-3 text-center text-red-600 font-semibold">{{ $v->NotPaid }}</td>
                                <!-- 7. Gap -->
                                <td class="p-3 text-center font-bold text-orange-600 bg-orange-50/30">
                                    {{ $v->Gap }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-10 text-gray-500">
                                    No matching village data recorded in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- SUMMARY ROW FOOTER -->
                    @if ($villagesData->count() > 0)
                        <tfoot class="bg-slate-100 border-t-2 border-slate-300 font-bold text-gray-800">
                            <tr>
                                <td class="p-4 text-left font-bold text-blue-900 text-base">Gross Total</td>
                                <!-- Link tracking is removed from footer summary cell since it represents aggregate of all rows -->
                                <td class="p-4 text-center text-base text-gray-800 font-bold">{{ $grossTotal->Beneficiaries }}</td>
                                <td class="p-4 text-center text-base">{{ $grossTotal->Allotment }}</td>
                                <td class="p-4 text-center text-base">{{ $grossTotal->AssignedFlats }}</td>
                                <td class="p-4 text-center text-green-700 text-base font-bold">{{ $grossTotal->Paid }}</td>
                                <td class="p-4 text-center text-red-700 text-base font-bold">{{ $grossTotal->NotPaid }}</td>
                                <td class="p-4 text-center text-orange-700 text-base font-extrabold bg-orange-100/30">
                                    {{ $grossTotal->Gap }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- TAILWIND PAGINATION FOOTER LINKS -->
            <div class="px-6 py-4 border-t bg-slate-50">
                {{ $villagesData->links('pagination::tailwind') }}
            </div>

        </div>
    </main>

@endsection
