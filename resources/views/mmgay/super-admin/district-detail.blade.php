@extends('layouts.mmgayAdmin')

@section('title', 'Super Admin Dashboard')

@section('content')

    <main class="p-6 ml-[260px] mt-16 bg-slate-100 min-h-screen">

        <!-- HEADER -->
        <div class="bg-white p-5 rounded-xl shadow mb-6">
            <h2 class="text-xl font-bold text-gray-800">
                {{ $district->DistrictName }} - Analytics
            </h2>
            <p class="text-sm text-gray-500">Deep drill-down analysis</p>
        </div>

        <!-- KPI ROW -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Villages</p>
                <h2 class="text-2xl font-bold text-blue-600">{{ $summary->TotalVillages }}</h2>
            </div>

            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Beneficiaries</p>
                <h2 class="text-2xl font-bold text-indigo-600">{{ $summary->TotalBeneficiaries }}</h2>
            </div>

            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Paid</p>
                <h2 class="text-2xl font-bold text-green-600">{{ $summary->Paid }}</h2>
            </div>

            <div class="bg-white p-4 rounded-xl shadow text-center">
                <p class="text-gray-500">Not Paid</p>
                <h2 class="text-2xl font-bold text-red-600">{{ $summary->NotPaid }}</h2>
            </div>

        </div>

        <!-- VILLAGE LIST -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="p-4 border-b font-semibold">
                Villages in {{ $district->DistrictName }}
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Village</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($villages as $v)
                        <tr class="border-t">
                            <td class="p-3">{{ $v->VillageName }}</td>
                            <td class="p-3">
                                <a href="#" class="text-blue-600 font-semibold">
                                    View Details →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </main>

@endsection
