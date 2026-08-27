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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            overflow: hidden; /* Prevent body scrollbars */
        }
    </style>
</head>
<body class="h-screen bg-[#f4f7f6] text-slate-800 flex">

    <!-- INCLUDE SIDEBAR PARTIAL -->
    @include('ews.department.partials.sidebar')

    <!-- MAIN CONTAINER (Pushed right by 260px) -->
    <div class="ml-[260px] flex-grow flex flex-col h-screen overflow-hidden">
        
        <!-- Header -->
        <header class="bg-white border-b border-slate-200 px-6 py-2.5 flex items-center justify-between sticky top-0 z-30 shadow-sm shrink-0">
            <h1 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-650 text-xl font-bold">manage_accounts</span>
                <div class="flex-grow">
                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider leading-none">My Profile Settings</h2>
                </div>
            </h1>
            <div class="flex items-center gap-2">
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-700 leading-none">{{ $user->name }}</p>
                    <p class="text-[8px] text-blue-650 font-bold uppercase tracking-wider mt-0.5 leading-none">EWS ADMINISTRATOR</p>
                </div>
                <div class="w-7 h-7 bg-blue-50 text-blue-700 font-black rounded-lg flex items-center justify-center border border-blue-100 text-[10px]">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6 flex-grow overflow-y-auto">
            <div class="w-full bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden shrink-0">
                <!-- Header Banner -->
                <div class="bg-gradient-to-r from-slate-900 to-slate-850 px-5 py-3 text-white flex items-center justify-between">
                    <div>
                        <span class="px-1.5 py-0.5 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded text-[8px] font-black uppercase mb-0.5 inline-block leading-none">
                            System Role: {{ str_replace('_', ' ', $user->role) }}
                        </span>
                        <h3 class="text-xs font-black text-white uppercase tracking-wider leading-none">{{ $user->name }}</h3>
                        <p class="text-[9px] text-slate-400 font-bold font-mono mt-1 leading-none">{{ $user->email }}</p>
                    </div>
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                        <span class="material-symbols-outlined text-lg">admin_panel_settings</span>
                    </div>
                </div>

                <form action="{{ route('ews.department.profile.update', $user->secure_id) }}" method="POST" class="p-4 space-y-3.5">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="p-2.5 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-[10px] space-y-0.5 leading-none">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-center gap-1 font-bold"><span class="material-symbols-outlined text-sm">warning</span> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        <!-- Admin Name (EDITABLE) -->
                        <div class="space-y-1">
                            <label for="name" class="block text-[9px] font-black uppercase text-slate-500 tracking-wider">
                                Department Admin Name <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-9 py-1.5 text-xs text-slate-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                                <span class="material-symbols-outlined absolute right-3 top-2 text-slate-400 text-sm">edit</span>
                            </div>
                        </div>

                        <!-- Email Address (LOCKED & READONLY) -->
                        <div class="space-y-1">
                            <label for="email" class="block text-[9px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between">
                                <span>System Email Address</span>
                                <span class="text-blue-650 font-bold text-[8px] flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">lock</span> Locked</span>
                            </label>
                            <div class="relative">
                                <input type="email" id="email" value="{{ $user->email }}" readonly disabled
                                    class="w-full bg-slate-100 border border-slate-200 rounded-lg pl-3 pr-9 py-1.5 text-xs text-slate-500 font-mono font-bold cursor-not-allowed select-none">
                                <span class="material-symbols-outlined absolute right-3 top-2 text-blue-500 text-sm">lock</span>
                            </div>
                        </div>

                        <!-- Mobile Number (EDITABLE) -->
                        <div class="space-y-1">
                            <label for="mobile" class="block text-[9px] font-black uppercase text-slate-500 tracking-wider">
                                Mobile Number
                            </label>
                            <div class="relative">
                                <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="Enter 10-digit mobile number" maxlength="10"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-9 py-1.5 text-xs text-slate-900 font-bold focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                                <span class="material-symbols-outlined absolute right-3 top-2 text-slate-400 text-sm">edit</span>
                            </div>
                        </div>

                        <!-- Assigned Role / Scheme (LOCKED & READONLY) -->
                        <div class="space-y-1">
                            <label for="role" class="block text-[9px] font-black uppercase text-slate-500 tracking-wider flex items-center justify-between">
                                <span>Assigned Role & Scheme</span>
                                <span class="text-blue-650 font-bold text-[8px] flex items-center gap-0.5"><span class="material-symbols-outlined text-[10px]">verified_user</span> Verified</span>
                            </label>
                            <input type="text" id="role" value="EWS DEPARTMENT ADMIN (EWS HOUSING)" readonly disabled
                                class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-500 font-mono font-bold cursor-not-allowed select-none">
                        </div>
                    </div>

                    <hr class="border-slate-100 my-2">

                    <!-- Change Password Section (EDITABLE) -->
                    <div class="space-y-2">
                        <h3 class="text-[9px] font-black uppercase text-slate-550 tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-blue-500 text-sm">key</span>
                            Change Password Credentials (Optional)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <!-- New Password -->
                            <div class="space-y-1">
                                <label for="password" class="block text-[9px] font-black uppercase text-slate-500 tracking-wider">New Password</label>
                                <input type="password" id="password" name="password" placeholder="Leave blank to keep current"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-900 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-1">
                                <label for="password_confirmation" class="block text-[9px] font-black uppercase text-slate-500 tracking-wider">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-900 font-medium focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-150 mt-1">
                        <a href="{{ route('ews.department.dashboard') }}" class="px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold uppercase rounded-lg text-[10px] transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-black uppercase tracking-wider rounded-lg text-[10px] shadow-sm transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">save</span>
                            <span>Update Profile Info</span>
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
                iconColor: '#2563eb'
            });
        @endif
    </script>
</body>
</html>
