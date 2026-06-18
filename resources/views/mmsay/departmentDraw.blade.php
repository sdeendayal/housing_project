@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">

        <div class="max-w-6xl mx-auto space-y-4">

            <div>
                <h3 class="text-lg font-medium text-primary">
                    Lucky Draw
                </h3>
            </div>

            <div class="overflow-x-auto bg-white shadow-sm rounded-xl border border-gray-100">

                <table class="min-w-full text-xs text-left text-gray-600">

                    <!-- HEADER -->
                    <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-center">Sr. No.</th>
                            <th class="px-4 py-3">District Name</th>
                            <th class="px-4 py-3 text-center">Total Assets</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>

                    <!-- BODY -->
                    <tbody class="divide-y divide-gray-100">

                        @foreach ($districts as $d)
                            <tr class="hover:bg-gray-50 transition">

                                <!-- SR NO -->
                                <td class="px-4 py-3 text-center text-gray-500">
                                    {{ $loop->iteration }}
                                </td>

                                <!-- District -->
                                <td class="px-4 py-3 font-medium text-gray-700">
                                    <a href="{{ url('department-draw/details/' . $d->DistrictId) }}"
                                        class="hover:text-blue-600 transition">
                                        {{ $d->DistrictName }}
                                    </a>
                                </td>

                                <!-- Assets -->
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-1 text-[11px] rounded-full bg-gray-100 text-gray-700 font-semibold">
                                        {{ $d->total_assets }}
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ url('/mmsay-department-draw/details/' . $d->DistrictId) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium
                              bg-blue-50 text-blue-600 rounded-md
                              hover:bg-blue-100 hover:text-blue-700 transition">
                                        🔍 View
                                    </a>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                    <!-- TOTAL -->
                    <tfoot class="bg-gray-50 text-xs font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-gray-700 text-center">TOTAL</td>

                            <td class="px-4 py-3"></td>

                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full bg-gray-900 text-white">
                                    {{ $grandTotal }}
                                </span>
                            </td>

                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>

                </table>

            </div>

        </div>
    </main>


@endsection
