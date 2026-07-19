<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Admin Profile - EWS Housing Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-800 flex flex-col">

    <!-- INCLUDE SIDEBAR PARTIAL -->
    @include('ews.department.partials.sidebar')

    <!-- MAIN CONTAINER (Pushed right by 260px) -->
    <div class="ml-[260px] flex-grow flex flex-col min-h-screen">
        
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span class="material-symbols-outlined text-orange-600 text-2xl">manage_accounts</span>
                    Department Admin Profile
                </h1>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Manage administrator credentials & profile information</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-900">{{ $user->name }}</p>
                    <p class="text-[10px] text-orange-600 font-bold uppercase tracking-wider">EWS ADMINISTRATOR</p>
                </div>
                <div class="w-9 h-9 bg-orange-100 text-orange-700 font-bold rounded-lg flex items-center justify-center border border-orange-200">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-8 flex-grow">
            <div class="max-w-3xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- Header Banner -->
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-6 py-5 text-white flex items-center justify-between">
                    <div>
                        <span class="px-2 py-0.5 bg-orange-500/20 text-orange-300 border border-orange-500/30 rounded text-[9px] font-black uppercase mb-1 inline-block">
                            SECURE ACCESS CONTROL
                        </span>
                        <h2 class="text-sm font-black uppercase tracking-wider">Department Admin Profile Details</h2>
                        <p class="text-[10px] text-slate-300 font-medium">Administrator Account Settings & Credentials</p>
                    </div>
                    <div class="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center text-white shadow-md">
                        <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                    </div>
                </div>

                <form action="{{ route('ews.department.profile.update', $user->secure_id) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-center gap-1 font-bold"><span class="material-symbols-outlined text-sm">warning</span> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Admin Name (EDITABLE) -->
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold uppercase text-slate-600 tracking-wider">
                                Department Admin Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full bg-slate-50 border border-slate-300 rounded-lg pl-3 pr-9 py-2.5 text-xs text-slate-900 font-bold focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none transition-all">
                                <span class="material-symbols-outlined absolute right-3 top-2.5 text-slate-400 text-base">edit</span>
                            </div>
                        </div>

                        <!-- Email Address (LOCKED & READONLY) -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                <span>System Email Address</span>
                                <span class="text-amber-600 font-bold text-[9px] flex items-center gap-0.5"><span class="material-symbols-outlined text-xs">lock</span> Locked</span>
                            </label>
                            <div class="relative">
                                <input type="email" id="email" value="{{ $user->email }}" readonly disabled
                                    class="w-full bg-slate-100 border border-slate-200 rounded-lg pl-3 pr-9 py-2.5 text-xs text-slate-500 font-mono font-bold cursor-not-allowed select-none">
                                <span class="material-symbols-outlined absolute right-3 top-2.5 text-amber-500 text-base">lock</span>
                            </div>
                            <p class="text-[9px] text-slate-400 italic">System email address cannot be edited.</p>
                        </div>

                        <!-- Mobile Number (EDITABLE) -->
                        <div class="space-y-1.5">
                            <label for="mobile" class="block text-xs font-bold uppercase text-slate-600 tracking-wider">
                                Mobile Number
                            </label>
                            <div class="relative">
                                <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="Enter 10-digit mobile number" maxlength="10"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-lg pl-3 pr-9 py-2.5 text-xs text-slate-900 font-bold focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none transition-all">
                                <span class="material-symbols-outlined absolute right-3 top-2.5 text-slate-400 text-base">edit</span>
                            </div>
                        </div>

                        <!-- Assigned Role / Scheme (LOCKED & READONLY) -->
                        <div class="space-y-1.5">
                            <label for="role" class="block text-xs font-bold uppercase text-slate-600 tracking-wider flex items-center justify-between">
                                <span>Assigned Role & Scheme</span>
                                <span class="text-amber-600 font-bold text-[9px] flex items-center gap-0.5"><span class="material-symbols-outlined text-xs">verified_user</span> System Role</span>
                            </label>
                            <input type="text" id="role" value="EWS DEPARTMENT ADMINISTRATOR (EWS HOUSING)" readonly disabled
                                class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-500 font-mono font-bold cursor-not-allowed select-none">
                        </div>
                    </div>

                    <hr class="border-slate-200 my-6">

                    <!-- Change Password Section (EDITABLE) -->
                    <div>
                        <h3 class="text-xs font-black uppercase text-slate-800 tracking-wider flex items-center gap-1.5 mb-4">
                            <span class="material-symbols-outlined text-amber-500 text-base">key</span>
                            Change Password Credentials (Optional)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- New Password -->
                            <div class="space-y-1.5">
                                <label for="password" class="block text-xs font-bold uppercase text-slate-600 tracking-wider">New Password</label>
                                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2.5 text-xs text-slate-900 font-medium focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none transition-all">
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-1.5">
                                <label for="password_confirmation" class="block text-xs font-bold uppercase text-slate-600 tracking-wider">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2.5 text-xs text-slate-900 font-medium focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-200">
                        <a href="{{ route('ews.department.dashboard') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold uppercase rounded-lg text-xs transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-black uppercase tracking-wider rounded-lg text-xs shadow-md transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-base">save</span>
                            <span>Update Admin Profile</span>
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Alert / Toast Messages via SweetAlert -->
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
                iconColor: '#ea580c'
            });
        @endif
    </script>
</body>
</html>
