<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f4f7fa] text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EWS Developer - Profile Settings</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .code-font { font-family: 'Fira Code', monospace; }
        .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f8fafc; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dev-shadow { box-shadow: 0 10px 30px -15px rgba(59, 130, 246, 0.08); }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#f4f7fa]">

    <!-- DEEP NAVY / SLATE SIDEBAR -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-300 shrink-0 h-full shadow-xl z-20">
        <!-- Brand logo -->
        <div class="h-16 px-6 border-b border-slate-800 flex items-center gap-2.5 shrink-0 bg-slate-950">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="bi bi-shield-fill-check text-white text-sm"></i>
            </div>
            <div>
                <h1 class="text-xs font-black tracking-tight text-white uppercase">EWS Portal</h1>
                <p class="text-[8px] text-slate-500 font-mono tracking-widest uppercase">Developer Hub</p>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scroll">
            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Registry Matrix</span>
                <div class="space-y-1">
                    <a href="{{ route('ews.developer.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-speedometer2 text-slate-400"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-building text-sky-400"></i>
                        <span>{{ !empty($user->district_name) ? strtoupper($user->district_name) : 'My District' }} Flats</span>
                    </a>
                    <a href="{{ route('ews.developer.dashboard', ['ownership_scope' => 'my_flats']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-person-check-fill text-emerald-400"></i>
                        <span>Flats Added By Me</span>
                    </a>
                    <a href="{{ route('ews.developer.flats.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-plus-circle text-slate-400"></i>
                        <span>Register Flat</span>
                    </a>
                </div>
            </div>

            <div>
                <span class="block px-3 text-[9px] font-black uppercase tracking-wider text-slate-400 mb-2">Account & Audit</span>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                        <i class="bi bi-person-gear text-sky-400"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('ews.developer.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white text-xs font-medium transition-all">
                        <i class="bi bi-journal-text text-slate-400"></i>
                        <span>Developer Logs</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Session Details -->
        <div class="p-4 border-t border-slate-800 bg-slate-950 flex flex-col gap-2 shrink-0">
            <a href="{{ route('ews.developer.logout') }}" class="w-full py-1.5 bg-red-500/20 hover:bg-red-600 text-red-300 rounded-lg text-[9px] font-black uppercase transition-all flex items-center justify-center gap-1 border border-red-500/30">
                <i class="bi bi-power"></i>
                <span>Logout Session</span>
            </a>
        </div>
    </aside>

    <!-- RIGHT WORKSPACE -->
    <div class="flex-1 flex flex-col overflow-hidden h-full">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('ews.developer.dashboard') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 text-xs font-bold flex items-center gap-1 transition-all">
                    <i class="bi bi-arrow-left"></i>
                    <span>Dashboard</span>
                </a>
                <div>
                    <h2 class="text-xs font-black tracking-wider text-slate-800 uppercase">Account Profile Settings</h2>
                    <p class="text-[8px] text-slate-450 font-mono uppercase">Developer Profile & Credential Management</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-[10px] text-slate-700 font-bold">{{ $user->name }}</div>
                    <div class="text-[8px] text-slate-400 font-mono">Mobile: {{ $user->mobile }}</div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll">

            <!-- Profile Settings Card -->
            <div class="max-w-3xl mx-auto bg-white border border-slate-200 rounded-xl shadow-sm dev-shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-150 bg-slate-50/60 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center">
                            <i class="bi bi-person-circle text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Developer Profile Information</h3>
                            <p class="text-[8px] text-slate-400 font-mono uppercase">Update name, email, and password credentials</p>
                        </div>
                    </div>
                    @if(!empty($user->district_name))
                        <span class="px-2.5 py-1 bg-sky-100 text-sky-800 border border-sky-200 rounded-lg text-[9px] font-black uppercase">
                            <i class="bi bi-geo-alt-fill me-0.5"></i> {{ strtoupper($user->district_name) }} DISTRICT
                        </span>
                    @endif
                </div>

                <form action="{{ route('ews.developer.profile.update') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    @if ($errors->any())
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-center gap-1 font-semibold"><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Developer Name -->
                        <div class="space-y-1">
                            <label for="name" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Developer Name / Firm <span class="text-rose-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-1">
                            <label for="email" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-bold focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                        </div>

                        <!-- Mobile Number (LOCKED & READONLY) -->
                        <div class="space-y-1">
                            <label for="mobile" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between">
                                <span>Mobile Number (Locked)</span>
                                <span class="text-amber-600 font-bold text-[9px]"><i class="bi bi-lock-fill"></i> Verified by Admin</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="mobile" value="{{ $user->mobile }}" readonly disabled
                                    class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-500 font-mono font-bold cursor-not-allowed select-none">
                                <span class="absolute right-3 top-2.5 text-slate-400 text-xs">
                                    <i class="bi bi-shield-lock-fill text-amber-500"></i>
                                </span>
                            </div>
                            <p class="text-[8px] text-slate-400 italic">Mobile number is locked for security & OTP authentication.</p>
                        </div>

                        <!-- Assigned District (LOCKED & READONLY) -->
                        <div class="space-y-1">
                            <label for="district_name" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between">
                                <span>Assigned District (Locked)</span>
                                <span class="text-sky-600 font-bold text-[9px]"><i class="bi bi-geo-alt-fill"></i> System Assigned</span>
                            </label>
                            <input type="text" id="district_name" value="{{ strtoupper($user->district_name ?? 'NOT ASSIGNED') }}" readonly disabled
                                class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-500 font-mono font-bold cursor-not-allowed select-none">
                        </div>
                    </div>

                    <hr class="border-slate-150 my-4">

                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                        <i class="bi bi-key-fill text-amber-500"></i>
                        Change Password (Optional)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- New Password -->
                        <div class="space-y-1">
                            <label for="password" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">New Password</label>
                            <input type="password" id="password" name="password" placeholder="Leave blank to keep current"
                                class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-medium focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1">
                            <label for="password_confirmation" class="block text-[10px] font-black uppercase text-slate-500 tracking-wider">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password"
                                class="w-full bg-slate-50 border border-slate-250 rounded-lg px-3 py-2 text-xs text-slate-800 font-medium focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-150">
                        <a href="{{ route('ews.developer.dashboard') }}"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase rounded-lg text-xs transition-all">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-5 py-2 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-750 text-white font-black uppercase tracking-wider rounded-lg text-xs shadow-md transition-all flex items-center gap-1.5">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Save Profile Changes</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Alert Messages via SweetAlert -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'PROFILE UPDATED',
                text: "{{ session('success') }}",
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                background: '#ffffff',
                color: '#1e293b',
                iconColor: '#3b82f6'
            });
        @endif
    </script>
</body>
</html>
