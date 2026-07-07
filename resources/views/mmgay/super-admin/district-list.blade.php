@extends('layouts.mmgayAdmin')

@section('title', 'District List')

@section('content')

    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">

        <!-- PAGE CARD -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100">

                <div>
                    <h2 class="text-2xl font-bold text-gray-800">District List</h2>
                    <p class="text-sm text-gray-500 mt-1">All registered districts in system</p>
                </div>

                <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold text-sm shadow-sm">
                    Total: {{ count($data) }}
                </div>

            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <!-- HEADER -->
                    <thead class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-3 text-left">District</th>
                            <th class="p-3 text-center">Villages</th>
                            <th class="p-3 text-center">Applicants</th>
                            <th class="p-3 text-center">Number of Plots</th>
                            <th class="p-3 text-center">Paid</th>                            
                            <th class="p-3 text-center">Assigned Flats</th>   
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        @forelse($data as $d)
                            <tr class="hover:bg-slate-50 transition-colors">

                                <td class="p-3 font-semibold text-gray-700">
                                    {{ $d->DistrictName }}
                                </td>

                                <td class="p-3 text-center text-gray-600">{{ $d->VillagesWithPlots }}</td>
                                <td class="p-3 text-center text-gray-600">{{ $d->Beneficiaries }}</td>
                                <td class="p-3 text-center text-gray-600">{{ $d->Allotment }}</td>
                                <td class="p-3 text-center text-green-600 font-semibold">{{ $d->Paid }}</td>
                                
                                <td class="p-3 text-center text-gray-600">{{ $d->AssignedFlats ?? 0 }}</td>
                                

                               

                                

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center p-10 text-gray-500">
                                    No data found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- GROSS TOTAL FOOTER (Only visible if data exists) -->
                    @if (count($data) > 0)
                        <tfoot class="bg-slate-50 border-t-2 border-slate-300 font-bold text-gray-800">
                            <tr>
                                <td class="p-3 text-left font-bold text-blue-900 text-base">Gross Total</td>
                                <td class="p-3 text-center text-base">{{ $grossTotal->VillagesWithPlots }}</td>
                                <td class="p-3 text-center text-base">{{ $grossTotal->Beneficiaries }}</td>
                                 <td class="p-3 text-center text-base">{{ $grossTotal->Allotment }}</td>
                                <td class="p-3 text-center text-green-700 text-base">{{ $grossTotal->Paid }}</td>                               
                                <td class="p-3 text-center text-base">{{ $grossTotal->AssignedFlats }}</td>  
                            </tr>
                            </footer>
                    @endif

                </table>

            </div>

        </div>

    </main>

@endsection
