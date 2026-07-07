<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
@extends('layouts.mmgayAdmin')

@section('title', 'Allotment List')

@section('content')

    <main class="min-h-screen bg-slate-50 p-6 pt-24 ml-[260px]">

        <!-- Header Section -->
        <div
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Allotment List</h2>
                <p class="text-slate-400 text-sm mt-0.5">Approved Allotment Details & Beneficiaries</p>
            </div>

            <form method="GET" class="w-full md:w-auto relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search registration, name..."
                    class="border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 rounded-xl pl-9 pr-4 py-2 w-full md:w-72 outline-none transition text-sm text-slate-700 placeholder-slate-400">
            </form>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-gradient-to-r from-slate-800 to-indigo-900 text-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"><i
                                    class="fas fa-file-alt mr-2 text-indigo-300"></i>Registration</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"><i
                                    class="fas fa-user mr-2 text-indigo-300"></i>Owner</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"><i
                                    class="fas fa-phone mr-2 text-indigo-300"></i>Mobile</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"><i
                                    class="fas fa-map-marker-alt mr-2 text-indigo-300"></i>District</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"><i
                                    class="fas fa-home mr-2 text-indigo-300"></i>Village</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider"><i
                                    class="fas fa-building mr-2 text-indigo-300"></i>Flat</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($allotments as $row)
                            <tr class="hover:bg-slate-50/80 transition duration-150">
                                <td class="px-6 py-4 text-sm font-medium text-slate-500">
                                    {{ $loop->iteration + ($allotments->currentPage() - 1) * $allotments->perPage() }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-indigo-600 tracking-wide">
                                    {{ $row->RegistrationNo }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800">
                                    {{ $row->OwnerName }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fas fa-phone text-emerald-500 text-xs"></i>
                                        {{ $row->MobileNo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md text-xs font-medium border border-slate-200">
                                        {{ $row->DistrictName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $row->VillageName }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-indigo-100">
                                        {{ $row->FlatNo }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if ($row->IsPaid)
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-check-circle text-xs"></i> Paid
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-700 border border-rose-200 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-times-circle text-xs"></i> Not Paid
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <button data-id="{{ $row->OwnerId }}"
                                        class="viewBtn inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-medium text-xs px-3.5 py-2 rounded-xl shadow-sm hover:shadow transition-all duration-200 transform active:scale-95">
                                        <i class="fas fa-eye text-xs"></i> View Info
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-16 bg-slate-50/50">
                                    <div class="max-w-xs mx-auto text-center">
                                        <div class="inline-flex p-4 rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <i class="fas fa-folder-open text-3xl"></i>
                                        </div>
                                        <h3 class="text-slate-700 font-semibold text-base">No Allotments Found</h3>
                                        <p class="text-slate-400 text-xs mt-1">We couldn't find any data matching your
                                            request.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div
            class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-xs font-medium text-slate-500">
            </div>

            <div class="flex items-center space-x-1 modern-pagination">
                {{ $allotments->withQueryString()->links('pagination::tailwind') }}
            </div>
        </div>
    </main>

    <!-- Modal Layout Modernized -->
    <div id="detailModal"
        class="fixed inset-0 hidden z-50 bg-slate-900/40 backdrop-blur-md items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden animate__animated animate__zoomIn animate__faster">

            <!-- Modal Header -->
            <div
                class="bg-gradient-to-r from-slate-800 to-indigo-900 text-white px-6 py-4 flex justify-between items-center shrink-0 border-b border-indigo-950/20">
                <div>
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-id-card text-indigo-400"></i> Beneficiary Profile Details
                    </h2>
                    <p class="text-indigo-200/80 text-xs mt-0.5">Complete allocated asset documentation</p>
                </div>
                <button id="closeModal"
                    class="h-9 w-9 rounded-xl bg-white/10 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all duration-200">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto bg-slate-50/50 grow">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm md:col-span-2">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Owner
                            Name</label>
                        <div id="OwnerName" class="font-extrabold text-slate-800 text-lg mt-0.5 tracking-tight"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Father /
                            Husband</label>
                        <div id="FatherName" class="font-semibold text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Registration
                            No</label>
                        <div id="RegistrationNo" class="font-bold text-indigo-600 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">PPP ID</label>
                        <div id="PPPId" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    {{-- <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Member ID</label>
                        <div id="MemberId" class="font-medium text-slate-700 mt-1"></div>
                    </div> --}}

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Gender</label>
                        <div id="Gender" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Mobile</label>
                        <div id="Mobile" class="font-semibold text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">District</label>
                        <div id="District" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Block</label>
                        <div id="Block" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Village</label>
                        <div id="Village" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-indigo-100 shadow-sm bg-indigo-50/30">
                        <label class="text-[11px] font-bold tracking-wider text-indigo-500 uppercase block">Flat No</label>
                        <div id="Flat" class="font-bold text-indigo-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Phase</label>
                        <div id="Phase" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm lg:col-span-1">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Category</label>
                        <div id="Caste" class="font-medium text-slate-700 mt-1"></div>
                    </div>

                    <div class="bg-white rounded-xl p-4 border border-slate-200/60 shadow-sm lg:col-span-2">
                        <label class="text-[11px] font-bold tracking-wider text-slate-400 uppercase block">Address</label>
                        <div id="Address" class="text-sm text-slate-600 mt-1"></div>
                    </div>

                    <div
                        class="bg-amber-50/50 rounded-xl p-4 border border-amber-200/70 shadow-sm lg:col-span-1 md:col-span-2">
                        <label class="text-[11px] font-bold tracking-wider text-amber-600 uppercase block">Remarks</label>
                        <div id="Remarks" class="text-sm text-amber-900 font-medium mt-1"></div>
                    </div>

                    <div
                        class="bg-emerald-50/50 rounded-xl p-4 border border-emerald-200/70 shadow-sm lg:col-span-2 md:col-span-2">
                        <label class="text-[11px] font-bold tracking-wider text-emerald-600 uppercase block">DC
                            Remarks</label>
                        <div id="DCRemarks" class="text-sm text-emerald-900 font-medium mt-1"></div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer Optional -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 shrink-0 text-right">
                <button type="button" onclick="$('#closeModal').click();"
                    class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium text-xs rounded-xl transition">
                    Dismiss
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(function() {
            // Maine alerts hta diye hain kyuki vo bar-bar user experience kharab karte hain, dynamic render automatic check ho jayega.
            $(document).on('click', '.viewBtn', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: "/super-admin/allotment-details/" + id,
                    type: "GET",
                    success: function(res) {
                        $('#OwnerName').text(res.data.OwnerName || 'N/A');
                        $('#FatherName').text(res.data.FatherHusbandName || 'N/A');
                        $('#RegistrationNo').text(res.data.RegistrationNo || 'N/A');
                        $('#PPPId').text(res.data.PPPId || 'N/A');
                        $('#MemberId').text(res.data.MemberId || 'N/A');
                        $('#Gender').text(res.data.Gender || 'N/A');
                        $('#Mobile').text(res.data.MobileNo || 'N/A');
                        $('#District').text(res.data.DistrictName || 'N/A');
                        $('#Block').text(res.data.BlockName || 'N/A');
                        $('#Village').text(res.data.VillageName || 'N/A');
                        $('#Flat').text(res.data.FlatNo || 'N/A');
                        $('#Phase').text(res.data.Phase || 'N/A');
                        $('#Caste').text(res.data.Caste || 'N/A');
                        $('#Address').text(res.data.OwnerAddress || 'N/A');
                        $('#Remarks').text(res.data.Remarks || 'No Remarks');
                        $('#DCRemarks').text(res.data.DCRemarks || 'No DC Remarks');

                        $('#detailModal').removeClass('hidden').addClass('flex');
                    }
                });
            });

            $('#closeModal').click(function() {
                $('#detailModal').addClass('hidden').removeClass('flex');
            });

            // Modal clear wrapper if clicking background overlay
            $('#detailModal').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden').removeClass('flex');
                }
            });
        });
    </script>
@endsection
