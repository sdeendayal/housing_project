@extends('layouts.mmsayDepartmentAuth')
@section('title', 'MMSAY Department Dashboard')
@section('content')
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">

        <div class="max-w-6xl mx-auto space-y-4">

            <div class="flex items-center justify-between mb-4">

                <!-- LEFT: Title -->
                <h3 class="text-lg font-medium text-primary">
                    Lucky Draw
                </h3>

                <!-- RIGHT: Back Button -->
                <a href="{{ url('mmsay-department-draw') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-semibold rounded-lg shadow transition">
                    ⬅ Back
                </a>

            </div>

            <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">

                <!-- TOP INFO -->
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg font-bold text-gray-800">
                        District: {{ $districtName }}
                    </h2>

                    <span class="px-3 py-1 text-sm bg-black text-white rounded-full">
                        Total Records: {{ $totalRecords }}
                    </span>
                </div>

                <!-- TABLE -->
                <table class="w-full text-sm border border-gray-300">

                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="p-2 border text-left">Sr. No.</th>
                            <th class="p-2 border text-left">Asset ID</th>
                            <th class="p-2 border text-left">Name</th>
                            <th class="p-2 border text-left">Size</th>
                            <th class="p-2 border text-left">Unit</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="p-2 border">{{ $loop->iteration }}</td>
                                <td class="p-2 border">{{ $row->AssetId }}</td>
                                <td class="p-2 border">{{ $row->AssetName }}</td>
                                <td class="p-2 border">{{ $row->AssetSize }}</td>
                                <td class="p-2 border">{{ $row->Unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

                <!-- PAGINATION -->
                <div class="mt-4">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection
