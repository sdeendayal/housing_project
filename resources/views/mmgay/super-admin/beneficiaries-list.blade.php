@extends('layouts.mmgayAdmin')

@section('title', 'Beneficiaries Master List')

@section('content')
    <main class="min-h-screen bg-slate-100 p-6 pt-20 ml-[260px] w-[calc(100%-260px)]">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <!-- HEADER -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b bg-gradient-to-r from-slate-50 to-gray-100 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Beneficiaries Directory</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage and view detailed profiles of registered beneficiaries</p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-semibold text-sm shadow-sm">
                    Total: {{ $beneficiaries->total() }}
                </div>
            </div>

            <!-- SEARCH & ADVANCED FILTERS BAR -->
            <div class="p-4 bg-slate-50 border-b border-gray-200">
                <form action="{{ url()->current() }}" method="GET"
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end">

                    <!-- 1. Text Search -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Search Beneficiary</label>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Name, Mobile or PPP ID..."
                            class="w-full px-4 py-2 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500 border-gray-300 focus:outline-none">
                    </div>

                    <!-- 2. Phase Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Phase</label>
                        <select name="phase"
                            class="w-full px-4 py-2 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500 border-gray-300 focus:outline-none">
                            <option value="">-- All Phases --</option>
                            <option value="1" {{ $phaseFilter == '1' ? 'selected' : '' }}>Phase 1</option>
                            <option value="2" {{ $phaseFilter == '2' ? 'selected' : '' }}>Phase 2</option>
                            <option value="3" {{ $phaseFilter == '3' ? 'selected' : '' }}>Phase 3</option>
                        </select>
                    </div>

                    <!-- 3. District Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">District</label>
                        <select name="district_id" id="districtSelect"
                            class="w-full px-4 py-2 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500 border-gray-300 focus:outline-none">
                            <option value="">-- All Districts --</option>
                            @foreach ($districts as $d)
                                <option value="{{ $d->DistrictId }}"
                                    {{ $districtFilter == $d->DistrictId ? 'selected' : '' }}>{{ $d->DistrictName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Village Filter -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Village</label>
                        <select name="village_id" id="villageSelect"
                            class="w-full px-4 py-2 border rounded-xl bg-white text-sm focus:ring-2 focus:ring-blue-500 border-gray-300 focus:outline-none">
                            <option value="">-- All Villages --</option>
                            @foreach ($villages as $v)
                                <option value="{{ $v->VillageId }}"
                                    {{ $villageFilter == $v->VillageId ? 'selected' : '' }}>{{ $v->VillageName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Action Buttons -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition shadow-sm w-full">Apply</button>
                        <a href="{{ route('superadmin.beneficiaries.index') }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-xl text-sm transition text-center w-full">Clear</a>
                    </div>
                </form>
            </div>

            <!-- BASIC INFO TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-3 text-left">Beneficiary Name</th>
                            <th class="p-3 text-left">Father/Husband Name</th>
                            <th class="p-3 text-center">Mobile No</th>
                            <th class="p-3 text-center">Location</th>
                            <th class="p-3 text-center">Phase</th>
                            <th class="p-3 text-center">Flat/Plot No</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($beneficiaries as $b)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-3 font-bold text-gray-800">{{ $b->OwnerName }}</td>
                                <td class="p-3 text-gray-600">{{ $b->FatherHusbandName ?? '-' }}</td>
                                <td class="p-3 text-center text-gray-600">{{ $b->MobileNo ?? '-' }}</td>
                                <td class="p-3 text-center text-xs text-gray-500 font-semibold uppercase">
                                    {{ $b->VillageName }}, {{ $b->DistrictName }}</td>
                                <td class="p-3 text-center">
                                    <span
                                        class="bg-slate-100 text-slate-800 text-xs px-2.5 py-0.5 rounded-md font-bold">P{{ $b->Phase }}</span>
                                </td>
                                <td class="p-3 text-center font-semibold text-indigo-600">
                                    {{ $b->FlatNo ?? 'Pending Allotment' }}</td>
                                <td class="p-3 text-center">
                                    <button onclick="viewFullProfile({{ $b->OwnerId }})"
                                        class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition shadow-sm">
                                        View Full Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-10 text-gray-500">No records found matching the
                                    filter criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TAILWIND PAGINATION FOOTER LINKS -->
            <div class="px-6 py-4 border-t bg-slate-50">
                {{ $beneficiaries->links('pagination::tailwind') }}
            </div>

        </div>
    </main>

    <!-- ========================================================================= -->
    <!-- DYNAMIC PROFILE INFORMATION POP-UP MODAL (Completely Structured) -->
    <!-- ========================================================================= -->
    <div id="detailsModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden max-h-[90vh] flex flex-col">

            <!-- Header -->
            <div
                class="px-6 py-4 bg-gradient-to-r from-blue-700 to-indigo-800 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold" id="popOwnerName">Beneficiary Full Profile</h3>
                    <p class="text-xs text-blue-100 mt-0.5" id="popRegNo">Registration Details Master</p>
                </div>
                <button onclick="closeProfileModal()"
                    class="text-white hover:text-gray-200 text-2xl font-bold focus:outline-none">&times;</button>
            </div>

            <!-- Content Body -->
            <div class="p-6 overflow-y-auto space-y-6 flex-1 text-sm text-gray-700 bg-slate-50/50" id="popBody">

                <!-- Section 1: Personal Details Grid -->
                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <h4 class="text-xs uppercase tracking-wider text-indigo-600 font-bold mb-3 border-b pb-1.5">👤 Personal
                        Information</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div><span class="block text-xs text-gray-400">Gender</span><strong id="pGender"
                                class="text-gray-800">-</strong></div>
                        <div><span class="block text-xs text-gray-400">Relation</span><strong id="pRelation"
                                class="text-gray-800">-</strong></div>
                        <div><span class="block text-xs text-gray-400">Caste/Category</span><strong id="pCaste"
                                class="text-gray-800">-</strong></div>
                        <div><span class="block text-xs text-gray-400">PPP ID</span><strong id="pPPP"
                                class="text-gray-800">-</strong></div>
                        <div><span class="block text-xs text-gray-400">Member ID</span><strong id="pMember"
                                class="text-gray-800">-</strong></div>
                        <div>
                        <span class="block text-xs text-gray-400">Address</span>
                        <strong id="pAddress" class="text-gray-800">-</strong>
                    </div>
                </div>
            </div>

            <!-- Section 2: Location & Flat/Plot Details Grid -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <h4 class="text-xs uppercase tracking-wider text-emerald-600 font-bold mb-3 border-b pb-1.5">
                    🏠 Location & Flat/Plot Details
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-xs text-gray-400">District</span>
                        <strong id="pDistrict" class="text-gray-800">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Block</span>
                        <strong id="pBlock" class="text-gray-800">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Village</span>
                        <strong id="pVillage" class="text-gray-800">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Flat / Plot Number (FlatMaster)</span>
                        <strong id="pFlatNo" class="text-indigo-600 font-bold text-base">-</strong>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Phase Segment</span>
                        <strong id="pPhase" class="text-gray-800">-</strong>
                    </div>
                </div>
            </div>

            <!-- Section 3: Workflow & Payment Status Grid -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <h4 class="text-xs uppercase tracking-wider text-orange-600 font-bold mb-3 border-b pb-1.5">
                    📋 Workflow & Payment Status
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <span class="block text-xs text-gray-400">Is Approved</span>
                        <div id="pApproved"></div>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Is Rejected</span>
                        <div id="pRejected"></div>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Payment Paid</span>
                        <div id="pPaid"></div>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">Payment Approved</span>
                        <div id="pPayApproved"></div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Admin Remarks & Audit Logs -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <h4 class="text-xs uppercase tracking-wider text-red-600 font-bold mb-3 border-b pb-1.5">
                    💬 Admin Remarks & Auditing Notes
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="block text-xs text-gray-400">General Remarks</span>
                        <p id="pRemarks" class="text-gray-700 bg-slate-50 p-2 rounded-lg border text-xs min-h-[40px]"></p>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-400">DC / Executive Remarks</span>
                        <p id="pDCRemarks" class="text-gray-700 bg-slate-50 p-2 rounded-lg border text-xs min-h-[40px]"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT HANDLERS -->
<!-- ========================================================================= -->
<script>
// Dynamic village dropdown population on district change
document.getElementById('districtSelect').addEventListener('change', function() {
    const districtId = this.value;
    const villageSelect = document.getElementById('villageSelect');
    villageSelect.innerHTML = '<option value="">-- Loading Villages --</option>';
    
    if(!districtId) {
        villageSelect.innerHTML = '<option value="">-- All Villages --</option>';
        return;
    }
    
    fetch(`beneficiaries?district_id=${districtId}`)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newOptions = doc.getElementById('villageSelect').innerHTML;
            villageSelect.innerHTML = newOptions;
        });
});

function viewFullProfile(ownerId) {
    document.getElementById('detailsModal').classList.remove('hidden');
    
    // Ajax request execution
    fetch(`beneficiary-details/${ownerId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("HTTP error, status = " + response.status);
            }
            return response.json();
        })
        .then(res => {
            if(res.success) {
                const data = res.data;
                document.getElementById('popOwnerName').innerText = data.OwnerName ?? 'N/A';
                document.getElementById('popRegNo').innerText = "Reg No: " + (data.RegistrationNo ?? 'Not Set');
                
                // Section 1 fields data binding
                document.getElementById('pGender').innerText = data.Gender ?? '-';
                document.getElementById('pRelation').innerText = data.Relation ?? '-';
                document.getElementById('pCaste').innerText = data.CasteName ?? '-';
                document.getElementById('pPPP').innerText = data.PPPId ?? '-';
                document.getElementById('pAddress').innerText = data.OwnerAddress ?? '-';
                
                // Section 2 fields mapping
                document.getElementById('pDistrict').innerText = data.DistrictName ?? '-';
                document.getElementById('pBlock').innerText = data.BlockName ?? '-';
                document.getElementById('pVillage').innerText = data.VillageName ?? '-';
                document.getElementById('pFlatNo').innerText = data.FlatNo ?? 'Not Allotted Yet';
                document.getElementById('pPhase').innerText = "Phase " + (data.Phase ?? '-');

                // Section 3 badges generation
                document.getElementById('pApproved').innerHTML = data.IsApproved == 1 ? '<span class="px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Yes</span>' : '<span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">No</span>';
                document.getElementById('pRejected').innerHTML = data.IsRejected == 1 ? '<span class="px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">Rejected</span>' : '<span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">No</span>';
                document.getElementById('pPaid').innerHTML = data.IsPaid == 1 ? '<span class="px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full">Paid</span>' : '<span class="px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-600 rounded-full">Unpaid</span>';
                document.getElementById('pPayApproved').innerHTML = data.IsPaymentApproved == 1 ? '<span class="px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Approved</span>' : '<span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">No</span>';

                // Section 4 remarks mapping
                document.getElementById('pRemarks').innerText = data.Remarks ?? 'No administrative notes entered.';
                document.getElementById('pDCRemarks').innerText = data.DCRemarks ?? 'No executive DC notes logged.';
            } else {
                alert("Error: " + res.message);
                closeProfileModal();
            }
        })
        .catch(err => {
            console.error("Fetch API error:", err);
            alert("Database connectivity error. Please refresh the page.");
            closeProfileModal();
        });
}

function closeProfileModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}
</script>
@endsection