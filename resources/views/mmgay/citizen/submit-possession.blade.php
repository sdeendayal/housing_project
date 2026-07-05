<!DOCTYPE html>
<html class="light h-full" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Complete Physical Possession Submission</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .slot-radio:checked + .slot-card {
            border-color: #0058bc;
            background-color: #f0f7ff;
        }
        .slot-radio:checked + .slot-card .slot-icon {
            background-color: #0058bc;
            color: #ffffff;
        }
        .slot-radio:checked + .slot-card .slot-check {
            border-color: #0058bc;
            background-color: #0058bc;
        }
        .slot-radio:checked + .slot-card .slot-check::after {
            content: '';
            display: block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ffffff;
        }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="h-full flex flex-col justify-between">
    <!-- Navbar Header -->
    <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#0058bc] rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/10">
                <span class="material-symbols-outlined text-[20px] font-bold">holiday_village</span>
            </div>
            <div>
                <h1 class="text-sm font-extrabold text-slate-800 leading-tight">MMGAY Physical Possession</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Beneficiary Slot Selection</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-xs font-bold text-slate-800">{{ $user->name }}</p>
                <p class="text-[9px] text-slate-400 font-semibold uppercase">Beneficiary</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">
                {{ substr($user->name, 0, 2) }}
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-[800px] w-full mx-auto px-6 py-8">
        <a href="{{ route('mmgay.citizen.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-[#0058bc] mb-6 transition">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Back to Dashboard
        </a>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-6 text-xs font-semibold">
                <p class="font-bold mb-1 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    Errors occurred:
                </p>
                <ul class="list-disc pl-5 space-y-0.5 mt-1 font-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-[0_10px_30px_rgba(0,0,0,0.03)] border border-slate-100 overflow-hidden">
            <!-- Banner header -->
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50/50 to-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0058bc] flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px] font-bold">calendar_month</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800 text-base">Select Physical Visit Slot</h2>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">The officer has scheduled three slots. Choose one for the site engineer visit.</p>
                    </div>
                </div>
                <span class="bg-amber-50 text-amber-800 text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-lg border border-amber-200/40">Action Required</span>
            </div>

            <div class="p-6 space-y-6">
                <!-- Info Section -->
                <div class="bg-slate-50 border border-slate-200/60 rounded-2xl p-4 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <p class="text-slate-400">Application Number</p>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Applicant Name</p>
                        <p class="font-bold text-slate-800 mt-0.5">{{ $application->applicant_name }}</p>
                    </div>
                    <div class="col-span-2 border-t border-slate-200/40 pt-3">
                        <p class="text-slate-400">BDO Instructions</p>
                        <p class="font-medium text-slate-700 mt-1 italic">
                            "{{ $application->visit_instructions ?? 'Please bring original identity documents.' }}"
                        </p>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('mmgay.villager.submit.post') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="text-xs uppercase font-extrabold tracking-wider text-slate-500 block mb-3">
                            Click to select one slot:
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Slot 1 -->
                            @if($application->visit_slot_1)
                                <div class="relative">
                                    <input type="radio" name="selected_slot" id="slot_1" value="{{ $application->visit_slot_1->format('Y-m-d H:i:s') }}" class="slot-radio absolute opacity-0 w-0 h-0" required>
                                    <label for="slot_1" class="slot-card cursor-pointer block border border-slate-200 rounded-2xl p-4 bg-white hover:bg-slate-50/50 hover:border-slate-300 transition shadow-sm relative">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="slot-icon w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Option 1</span>
                                                    <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $application->visit_slot_1->format('d M Y') }}</span>
                                                    <span class="text-[10px] font-semibold text-[#0058bc] block mt-0.5">{{ $application->visit_slot_1->format('h:i A') }}</span>
                                                </div>
                                            </div>
                                            <div class="slot-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all"></div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            <!-- Slot 2 -->
                            @if($application->visit_slot_2)
                                <div class="relative">
                                    <input type="radio" name="selected_slot" id="slot_2" value="{{ $application->visit_slot_2->format('Y-m-d H:i:s') }}" class="slot-radio absolute opacity-0 w-0 h-0" required>
                                    <label for="slot_2" class="slot-card cursor-pointer block border border-slate-200 rounded-2xl p-4 bg-white hover:bg-slate-50/50 hover:border-slate-300 transition shadow-sm relative">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="slot-icon w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Option 2</span>
                                                    <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $application->visit_slot_2->format('d M Y') }}</span>
                                                    <span class="text-[10px] font-semibold text-[#0058bc] block mt-0.5">{{ $application->visit_slot_2->format('h:i A') }}</span>
                                                </div>
                                            </div>
                                            <div class="slot-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all"></div>
                                        </div>
                                    </label>
                                </div>
                            @endif

                            <!-- Slot 3 -->
                            @if($application->visit_slot_3)
                                <div class="relative">
                                    <input type="radio" name="selected_slot" id="slot_3" value="{{ $application->visit_slot_3->format('Y-m-d H:i:s') }}" class="slot-radio absolute opacity-0 w-0 h-0" required>
                                    <label for="slot_3" class="slot-card cursor-pointer block border border-slate-200 rounded-2xl p-4 bg-white hover:bg-slate-50/50 hover:border-slate-300 transition shadow-sm relative">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="slot-icon w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Option 3</span>
                                                    <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $application->visit_slot_3->format('d M Y') }}</span>
                                                    <span class="text-[10px] font-semibold text-[#0058bc] block mt-0.5">{{ $application->visit_slot_3->format('h:i A') }}</span>
                                                </div>
                                            </div>
                                            <div class="slot-check w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 transition-all"></div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('mmgay.citizen.dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-6 py-3 rounded-2xl text-xs transition">Cancel</a>
                        <button type="submit" class="bg-[#0058bc] hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-2xl text-xs shadow-md transition flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] font-bold">check_circle</span>
                            Confirm Selection
                        </button>
                    </div>
                </form>

                <!-- Timeline Progress Logs Section -->
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[#0058bc] text-lg font-bold">timeline</span>
                        Application Progress Timeline
                    </h3>
                    <div class="space-y-4 pl-2">
                        @forelse($logs as $log)
                            <div class="relative pl-5 border-l-2 border-slate-200 last:border-l-0 pb-1 text-xs">
                                <span class="absolute -left-[5.5px] top-1.5 w-2.5 h-2.5 rounded-full bg-blue-500 border border-white"></span>
                                <div class="flex items-center justify-between font-bold text-slate-700 text-[10px]">
                                    <span class="uppercase tracking-wider text-blue-600">
                                        {{ $log->new_status }}
                                    </span>
                                    <span class="text-slate-400 font-normal">
                                        {{ Carbon\Carbon::parse($log->created_at)->format('d M Y - h:i A') }}
                                    </span>
                                </div>
                                <p class="text-slate-500 text-[11px] mt-0.5 leading-normal">{{ $log->remarks }}</p>
                                <p class="text-[9px] text-slate-400 uppercase mt-0.5 font-bold tracking-wider">
                                    Action By: {{ $log->changed_by_type === 'officer' ? 'BDO Officer' : 'Applicant' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-slate-400 font-semibold text-[11px] py-1">No activity log found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-4 text-center text-[10px] text-slate-400 font-medium">
        © {{ date('Y') }} Haryana Gramin Development Authority. All Rights Reserved.
    </footer>
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            Swal.fire({
                title: 'Confirming Selection...',
                text: 'Please wait, updating your scheduled visit choice.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
</body>
</html>
