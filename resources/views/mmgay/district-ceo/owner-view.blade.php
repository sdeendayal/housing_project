@extends('layouts.mmgayCEOAuth')
@section('title', 'MMGAY - Owner Details')

@section('content')
    <main class="ml-[260px] mt-16 p-4 bg-gray-100 min-h-screen w-[calc(100%-260px)]">

        <div class="w-full bg-white rounded-2xl shadow-lg">
            <div class="p-8">

                <!-- Back -->
                <a href="{{ url()->previous() }}" class="text-blue-600 font-medium">← Back</a>

                <!-- ================= COMPLETE INFORMATION ================= -->

                <div class="grid lg:grid-cols-2 gap-8 mt-6">

                    <!-- ================= Personal Information ================= -->
                    <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">

                        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 px-6 py-5">

                            <div class="flex items-center gap-4">

                                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">

                                    <span class="material-symbols-outlined text-white text-3xl">
                                        person
                                    </span>

                                </div>

                                <div>

                                    <h2 class="text-2xl font-bold text-white">
                                        Personal Information
                                    </h2>

                                    <p class="text-blue-100 text-sm">
                                        Owner Details
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6">

                            <table class="w-full">

                                <tbody class="divide-y divide-gray-100">

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600 w-52">
                                            👤 Owner Name
                                        </td>
                                        <td class="font-semibold text-gray-900">
                                            {{ $owner->OwnerName }}
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            👨 Father / Husband
                                        </td>
                                        <td>{{ $owner->FatherHusbandName }}</td>
                                    </tr>

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🤝 Relation
                                        </td>
                                        <td>{{ $owner->Relation }}</td>
                                    </tr>

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🚻 Gender
                                        </td>
                                        <td>{{ $owner->Gender }}</td>
                                    </tr>

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            📞 Mobile Number
                                        </td>
                                        <td>{{ $owner->MobileNo }}</td>
                                    </tr>

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🆔 PPP ID
                                        </td>
                                        <td>{{ $owner->PPPId }}</td>
                                    </tr>

                                    {{-- <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            💳 Member ID
                                        </td>
                                        <td>{{ $owner->MemberId }}</td>
                                    </tr> --}}

                                    <tr class="hover:bg-blue-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🏷 Category
                                        </td>
                                        <td>{{ $owner->Caste }}</td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- ================= Property Information ================= -->

                    <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">

                        <div class="bg-gradient-to-r from-green-700 to-emerald-600 px-6 py-5">

                            <div class="flex items-center gap-4">

                                <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center">

                                    <span class="material-symbols-outlined text-white text-3xl">
                                        home
                                    </span>

                                </div>

                                <div>

                                    <h2 class="text-2xl font-bold text-white">
                                        Property Information
                                    </h2>

                                    <p class="text-green-100 text-sm">
                                        Property Details
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6">

                            <table class="w-full">

                                <tbody class="divide-y divide-gray-100">

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600 w-52">
                                            📄 Registration No
                                        </td>
                                        <td class="font-semibold text-gray-900">
                                            {{ $owner->RegistrationNo }}
                                        </td>
                                    </tr>

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🏙 District
                                        </td>
                                        <td>{{ $owner->DistrictName }}</td>
                                    </tr>

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🏢 Block
                                        </td>
                                        <td>{{ $owner->BlockName }}</td>
                                    </tr>

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🌾 Village
                                        </td>
                                        <td>{{ $owner->VillageName }}</td>
                                    </tr>

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🏠 Flat No
                                        </td>
                                        <td>{{ $owner->FlatNo }}</td>
                                    </tr>

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            📍 Owner Address
                                        </td>
                                        <td>{{ $owner->OwnerAddress }}</td>
                                    </tr>

                                    <tr class="hover:bg-green-50 transition">
                                        <td class="py-4 font-semibold text-gray-600">
                                            🚩 Phase
                                        </td>
                                        <td>
                                            <span class="px-4 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                                Phase {{ $owner->Phase }}
                                            </span>
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="bg-white rounded-2xl shadow border mt-6">

                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-lg">
                            Application Status
                        </h3>
                    </div>

                    <div class="p-6">

                        <div class="grid md:grid-cols-5 gap-4">

                            <div class="text-center">
                                <div class="w-14 h-14 mx-auto rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600">
                                        description
                                    </span>
                                </div>
                                <p class="font-semibold mt-3">Registered</p>
                            </div>

                            <div class="text-center">
                                <div
                                    class="w-14 h-14 mx-auto rounded-full {{ $owner->IsApproved ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined {{ $owner->IsApproved ? 'text-green-600' : 'text-gray-400' }}">
                                        verified
                                    </span>
                                </div>
                                <p class="font-semibold mt-3">Approved</p>
                            </div>

                            <div class="text-center">
                                <div
                                    class="w-14 h-14 mx-auto rounded-full {{ $owner->IsPaid ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined {{ $owner->IsPaid ? 'text-green-600' : 'text-gray-400' }}">
                                        payments
                                    </span>
                                </div>
                                <p class="font-semibold mt-3">Paid</p>
                            </div>

                            <div class="text-center">
                                <div
                                    class="w-14 h-14 mx-auto rounded-full {{ $owner->IsRejected ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined {{ $owner->IsRejected ? 'text-red-600' : 'text-gray-400' }}">
                                        cancel
                                    </span>
                                </div>
                                <p class="font-semibold mt-3">Rejected</p>
                            </div>

                            <div class="text-center">
                                <div
                                    class="w-14 h-14 mx-auto rounded-full {{ $owner->IsDcReconsidered ? 'bg-yellow-100' : 'bg-gray-100' }} flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined {{ $owner->IsDcReconsidered ? 'text-yellow-600' : 'text-gray-400' }}">
                                        refresh
                                    </span>
                                </div>
                                <p class="font-semibold mt-3">Reconsidered</p>
                            </div>

                        </div>

                        {{-- Remarks --}}
                        @if (!empty($owner->Remarks))
                            <div class="mt-6 border-t pt-5">
                                <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                    <span class="material-symbols-outlined text-blue-600 mr-2">
                                        chat
                                    </span>
                                    Remarks
                                </h4>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-gray-700">
                                    {{ $owner->Remarks }}
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
                <br>

                <hr>

                <!-- ===================== DECISION PANEL ===================== -->
                @if ($owner->IsPaid)
                    {{-- Payment Completed UI --}}
                @elseif($owner->IsApproved)
                    {{-- Application Approved UI --}}
                @elseif($owner->IsRejected)
                    {{-- Grievance Form --}}
                    <form action="{{ route('district.owner.grievance.submit', $owner->OwnerId) }}" method="POST">
                        @csrf

                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <h3 class="font-bold text-red-700">Application Rejected</h3>
                            <p class="mt-2">{{ $owner->DCRemarks }}</p>
                        </div>

                        <label class="font-semibold">Grievance</label>
                        <textarea name="grievance" rows="5" required class="w-full border rounded-xl p-3 mt-2"></textarea>

                        <button type="submit" class="mt-5 w-full bg-blue-600 text-white py-3 rounded-xl">
                            Submit Grievance
                        </button>
                    </form>
                @else
                    {{-- Pending --}}
                    <form action="{{ route('district.owner.action', $owner->OwnerId) }}" method="POST">
                        @csrf

                        <div class="mb-5">
                            <label class="block font-semibold mb-2">
                                Remarks <span class="text-red-500">*</span>
                            </label>

                            <textarea name="remarks" rows="5" required
                                class="w-full rounded-xl border border-gray-300 p-4 focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter remarks..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">

                            <button type="submit" name="action" value="approve"
                                class="bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold">
                                Approve
                            </button>

                            <button type="submit" name="action" value="reject"
                                class="bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-semibold">
                                Reject
                            </button>

                        </div>
                    </form>
                @endif

            </div>
        </div>
    </main>
@endsection
