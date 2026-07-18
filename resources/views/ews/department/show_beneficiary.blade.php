<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiary Profile | Housing for All Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-[#f3f6fc] text-slate-800 min-h-screen flex">

    <!-- 1. Left Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-[260px] flex flex-col py-6 z-40 bg-[#1e293b] text-slate-300 shadow-xl border-r border-slate-800">
        <!-- Logo -->
        <div class="px-6 mb-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-orange-600 to-amber-500 rounded-lg flex items-center justify-center text-white">
                <span class="material-symbols-outlined text-xl font-bold">business</span>
            </div>
            <div>
                <h1 class="text-md font-extrabold text-white leading-tight">EWS DEPT</h1>
                <p class="text-[9px] uppercase tracking-wider text-orange-400 font-bold">Housing Haryana</p>
            </div>
        </div>

        <!-- Collapsible Structured Sidebar Navigation Links -->
        <nav class="flex-grow px-3 space-y-3.5 overflow-y-auto text-xs">
            
            <!-- Dashboard Link -->
            <a href="{{ route('ews.department.dashboard') }}" class="w-full flex items-center gap-3 rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left font-bold">
                <span class="material-symbols-outlined text-base">dashboard</span>
                <span>Overview Dashboard</span>
            </a>

            <!-- Group 1: Registration Phase -->
            <div class="space-y-1">
                <div class="px-4 py-1 text-[8px] uppercase font-black tracking-wider text-slate-500">1. Registration Phase</div>
                <a href="{{ route('ews.department.list') }}?type=registered" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">list_alt</span>
                        <span>Registered</span>
                    </div>
                </a>
            </div>

            <!-- Group 2: Eligibility Rejections -->
            <div class="space-y-1">
                <div class="px-4 py-1 text-[8px] uppercase font-black tracking-wider text-slate-500">2. Eligibility Rejections</div>
                <a href="{{ route('ews.department.list') }}?type=rejected_ppp" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">cancel</span>
                        <span>PPP Exclusion</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=rejected_property" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">domain_disabled</span>
                        <span>Property in India</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=rejected_ownership" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">home_work</span>
                        <span>House Ownership</span>
                    </div>
                </a>
            </div>

            <!-- Group 3: Verification Visited/Absent -->
            <div class="space-y-1">
                <div class="px-4 py-1 text-[8px] uppercase font-black tracking-wider text-slate-500">3. Verification Visited/Absent</div>
                <a href="{{ route('ews.department.list') }}?type=eligible_draw" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">how_to_reg</span>
                        <span>Eligible for Draw</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=booking" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">verified</span>
                        <span>Visited</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=not_visited" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">warning</span>
                        <span>Absent</span>
                    </div>
                </a>
            </div>

            <!-- Group 4: ADC Verification Outcomes -->
            <div class="space-y-1">
                <div class="px-4 py-1 text-[8px] uppercase font-black tracking-wider text-slate-500">4. ADC Verification Outcomes</div>
                <a href="{{ route('ews.department.list') }}?type=adc_passed" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">check_circle_outline</span>
                        <span>Passed</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=adc_failed" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">error_outline</span>
                        <span>Failed</span>
                    </div>
                </a>
            </div>

            <!-- Group 5: Final Draw Allotment -->
            <div class="space-y-1">
                <div class="px-4 py-1 text-[8px] uppercase font-black tracking-wider text-slate-500">5. Final Draw Allotment</div>
                <a href="{{ route('ews.department.list') }}?type=all" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">groups</span>
                        <span>Total Beneficiaries</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=allotted" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">check_circle</span>
                        <span>Allotted</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=pending" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">hourglass_empty</span>
                        <span>Pending</span>
                    </div>
                </a>
                <a href="{{ route('ews.department.list') }}?type=draw_remaining" class="w-full flex items-center justify-between rounded-lg px-4 py-2 hover:bg-slate-800 hover:text-white transition-all text-left">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-base">hourglass_disabled</span>
                        <span>Unallotted Draw</span>
                    </div>
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="mt-auto px-6 pt-4 border-t border-slate-800">
            <div class="mb-4 px-2">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Active Scheme</p>
                <p class="text-xs font-bold text-orange-400 uppercase mt-0.5">EWS HOUSING</p>
            </div>
            <a href="{{ route('ews.department.logout') }}" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-rose-500 hover:bg-rose-950/30 hover:text-rose-400 transition-all font-bold">
                <span class="material-symbols-outlined text-base">logout</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- 2. Main Page Area -->
    <div class="flex-1 flex flex-col ml-[260px]">
        
        <!-- Top Header / Navbar -->
        <header class="fixed top-0 right-0 w-[calc(100%-260px)] z-50 h-16 flex justify-between items-center px-6 bg-white shadow-sm border-b border-slate-200">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.department.dashboard') }}" class="flex items-center text-slate-500 hover:text-slate-800 transition mr-2">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h2 class="text-md font-extrabold text-[#0f172a]">Beneficiary Details</h2>
                <div class="h-5 w-[1px] bg-slate-200"></div>
                <span class="text-xs text-slate-500 font-medium">EWS Registration Registry</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-700">{{ $user->name }}</p>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">EWS Administrator</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-sm">
                    EW
                </div>
            </div>
        </header>

        <!-- Content Body Wrapper -->
        <main class="mt-16 p-4 flex-grow flex items-center justify-center">

            <!-- Profile Details Card (Flexible width for EWS Data 1) -->
            <div class="w-full max-w-5xl bg-white rounded-xl border border-slate-200 shadow-lg p-5 flex flex-col my-2">
                
                <!-- Avatar & Status Header (Compact) -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center font-black text-xl uppercase shadow-md shadow-orange-500/10">
                            {{ substr($beneficiary->full_name ?? 'B', 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-md font-black text-slate-800 leading-tight uppercase">{{ $beneficiary->full_name }}</h2>
                            <p class="text-[8px] uppercase tracking-wider text-slate-400 mt-0.5">Beneficiary Application Record ({{ $beneficiary->type }})</p>
                        </div>
                    </div>
                    
                    @if($beneficiary->status === 'Allotted')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-250 text-[9px] font-black text-emerald-700 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Allotted
                        </span>
                    @elseif($beneficiary->status === 'Pending')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-55/15 border border-amber-300 text-[9px] font-black text-amber-700 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Pending
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-200 text-[9px] font-black text-blue-700 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            Registered
                        </span>
                    @endif
                </div>

                <!-- Basic Fields Grid (Compact) -->
                <div class="grid grid-cols-2 {{ ($beneficiary->type ?? '') === 'allotted' ? 'sm:grid-cols-5' : 'sm:grid-cols-4' }} gap-x-4 gap-y-2.5 py-4 border-b border-slate-100 text-[11px] font-bold text-slate-700">
                    <!-- Application Number -->
                    <div>
                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Application Number</span>
                        <span class="block uppercase">{{ $beneficiary->application_number ?? 'N/A' }}</span>
                    </div>

                    @if(($beneficiary->type ?? '') === 'allotted')
                        <!-- Flat Number Details -->
                        <div>
                            <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Flat Number / Description</span>
                            <span class="text-orange-600 block uppercase">{{ $beneficiary->flat_no ?? 'No Flat Allotted' }}</span>
                        </div>
                    @endif

                    <!-- District -->
                    <div>
                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">District</span>
                        <span class="block uppercase text-slate-800 font-extrabold">{{ $beneficiary->dist_name ?? 'N/A' }}</span>
                    </div>

                    <!-- Aadhar Card Number -->
                    <div>
                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Aadhar Number</span>
                        <span class="font-mono block">{{ $beneficiary->aadhar_no ?? 'N/A' }}</span>
                    </div>

                    <!-- Mobile Number -->
                    <div>
                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Mobile Number</span>
                        <span class="font-mono block">{{ $beneficiary->mobile_number ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Dynamic Rich Details (Compact Side-by-Side Grid) -->
                @if(!empty($beneficiary->address) || !empty($beneficiary->date_of_birth) || !empty($beneficiary->father_name) || !empty($beneficiary->monthly_income))
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 py-4">
                        
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <!-- SECTION 1: Personal Details & Address -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <h4 class="text-[10px] font-black uppercase text-orange-600 tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-md font-bold">person</span>
                                    Personal Details & Address
                                </h4>
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2.5 text-[11px] font-bold text-slate-700">
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">DOB</span>
                                        <span>{{ $beneficiary->date_of_birth ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Age</span>
                                        <span>{{ $beneficiary->age ?? 'N/A' }} Years</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Gender</span>
                                        <span class="uppercase">{{ $beneficiary->gender ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Marital Status</span>
                                        <span class="uppercase">{{ $beneficiary->MaritalStatus ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Caste</span>
                                        <span class="uppercase">{{ $beneficiary->caste ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Ward No</span>
                                        <span>Ward {{ $beneficiary->ward_no ?? 'N/A' }}</span>
                                    </div>
                                    <div class="col-span-3 border-t border-slate-200/65 pt-2">
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Full Address</span>
                                        <span class="uppercase text-[10px] leading-normal mt-0.5 block text-slate-600">{{ $beneficiary->address ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: Socio-Economic & Income -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <h4 class="text-[10px] font-black uppercase text-orange-600 tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-md font-bold">payments</span>
                                    Financial Status & Income
                                </h4>
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2.5 text-[11px] font-bold text-slate-700">
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Monthly Income</span>
                                        <span class="font-mono text-slate-800 font-extrabold">₹{{ $beneficiary->monthly_income ?? '0' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Verified (PPP)</span>
                                        <span class="uppercase">{{ $beneficiary->IncomeVerified ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Occupation</span>
                                        <span class="uppercase">{{ $beneficiary->occupation_source_of_income ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Pensioner</span>
                                        <span class="uppercase">{{ $beneficiary->are_youapensioner ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Family Count</span>
                                        <span>{{ $beneficiary->number_of_family_members ?? 'N/A' }} Members</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">House Type</span>
                                        <span class="uppercase">{{ $beneficiary->type_of_house ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <!-- SECTION 3: Amenities & Eligibility -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <h4 class="text-[10px] font-black uppercase text-orange-600 tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-md font-bold">rule</span>
                                    Ownership Declarations & Exclusions
                                </h4>
                                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-[11px] font-bold text-slate-700">
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">AC Installed</span>
                                        <span class="uppercase">{{ $beneficiary->do_you_have_an_air_conditioner ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Electricity Acc</span>
                                        <span class="font-mono">{{ $beneficiary->electricity_bill_account_no ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Other Property across India</span>
                                        <span class="uppercase text-slate-600 leading-snug">{{ $beneficiary->do_you_own_any_property_or_house_across_india ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Exclusion Trigger</span>
                                        <span class="uppercase text-rose-600 font-extrabold">{{ $beneficiary->exclusion ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 4: Family Details -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <h4 class="text-[10px] font-black uppercase text-orange-600 tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-md font-bold">family_restroom</span>
                                    Parents & Spouse Information
                                </h4>
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2.5 text-[11px] font-bold text-slate-700">
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Father's Name</span>
                                        <span class="uppercase">{{ $beneficiary->father_name ?? $beneficiary->fathers_full_name ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Mother's Name</span>
                                        <span class="uppercase">{{ $beneficiary->mother_name ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Spouse Name</span>
                                        <span class="uppercase">{{ $beneficiary->spouse_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 5: Vehicle Declarations -->
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <h4 class="text-[10px] font-black uppercase text-orange-600 tracking-wider mb-3 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-md font-bold">directions_car</span>
                                    Vehicle Declarations
                                </h4>
                                <div class="grid grid-cols-3 gap-x-4 gap-y-2.5 text-[11px] font-bold text-slate-700">
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Owns Vehicle</span>
                                        <span class="uppercase">{{ $beneficiary->vehicle_ownership ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Vehicle Type</span>
                                        <span class="uppercase">{{ $beneficiary->type_of_vehicle ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[8px] font-black uppercase text-slate-400 tracking-wider">Reg No.</span>
                                        <span class="font-mono uppercase">{{ $beneficiary->vehicle_registration_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                @endif

                <!-- Back Actions -->
                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                    <a href="{{ route('ews.department.dashboard') }}?type={{ $beneficiary->type }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-black uppercase tracking-wider transition flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <span>Back to Dashboard</span>
                    </a>
                </div>

            </div>

        </main>
    </div>

</body>
</html>
