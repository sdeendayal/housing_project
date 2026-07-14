@extends('layouts.mmgayAdmin')

@section('title', 'All Villages Master List - Super Admin')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">


            {{-- HEADER --}}
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100 gap-4">

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        All Villages Master List
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Village wise beneficiary and allotment statistics
                    </p>
                </div>


                <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold text-sm shadow-sm">
                    Total Villages:
                    {{ $villagesData->total() ?? 0 }}
                </div>

            </div>



            {{-- FILTER --}}
            <div class="p-4 bg-slate-50 border-b">

                <form method="GET" action="{{ url()->current() }}" class="flex flex-col md:flex-row gap-4">


                    <div class="w-full md:w-1/3">

                        <label class="text-xs font-semibold text-gray-600">
                            Search Village
                        </label>

                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Village name..."
                            class="w-full mt-1 px-4 py-2 border rounded-xl">

                    </div>



                    <div class="w-full md:w-1/3">

                        <label class="text-xs font-semibold text-gray-600">
                            District
                        </label>


                        <select name="district_id" class="w-full mt-1 px-4 py-2 border rounded-xl">


                            <option value="">
                                -- All Districts --
                            </option>


                            @foreach ($districtsList as $district)
                                <option value="{{ $district->DistrictId }}"
                                    @if ($districtFilter == $district->DistrictId) selected @endif>

                                    {{ $district->DistrictName }}

                                </option>
                            @endforeach


                        </select>

                    </div>




                    <div class="flex items-end gap-2">

                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-xl">

                            Apply

                        </button>


                        <a href="{{ url()->current() }}" class="px-5 py-2 bg-gray-200 rounded-xl">

                            Clear

                        </a>


                    </div>


                </form>

            </div>





            {{-- TABLE --}}

            <div class="overflow-x-auto">


                <table class="w-full text-sm">


                    <thead class="bg-blue-600 text-white">


                        <tr>


                            <th class="p-3 text-left">
                                Village
                            </th>


                            <th class="p-3 text-center">
                                Beneficiaries
                            </th>


                            <th class="p-3 text-center">
                                Allotment
                            </th>


                            <th class="p-3 text-center">
                                Assigned Flats
                            </th>


                            <th class="p-3 text-center">
                                Paid
                            </th>


                            <th class="p-3 text-center">
                                Gap
                            </th>


                        </tr>


                    </thead>




                    <tbody class="divide-y">


                        @forelse($villagesData as $v)
                            <tr class="hover:bg-gray-50">


                                <td class="p-3 font-semibold">

                                    {{ $v->VillageName }}

                                </td>



                                <td class="p-3 text-center">


                                    <a href="{{ route('superadmin.beneficiaries.index', [
                                        'district_id' => $v->DistrictId,
                                        'village_id' => $v->VillageId,
                                    ]) }}"
                                        class="text-blue-600 font-bold hover:underline">


                                        {{ number_format($v->Beneficiaries ?? 0) }}


                                    </a>


                                </td>




                                <td class="p-3 text-center">

                                    {{ number_format($v->Allotment ?? 0) }}

                                </td>



                                <td class="p-3 text-center">

                                    {{ number_format($v->AssignedFlats ?? 0) }}

                                </td>




                                <td class="p-3 text-center text-green-600 font-semibold">

                                    {{ number_format($v->Paid ?? 0) }}

                                </td>




                                <td class="p-3 text-center text-orange-600 font-bold">

                                    {{ number_format($v->Gap ?? 0) }}

                                </td>



                            </tr>



                        @empty


                            <tr>

                                <td colspan="6" class="text-center p-10 text-gray-500">

                                    No village data found

                                </td>

                            </tr>
                        @endforelse



                    </tbody>  
                </table>
            </div>
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t bg-slate-50">
                {{ $villagesData->links('pagination::tailwind') }}
            </div>       
        </div>
    </main>
@endsection
