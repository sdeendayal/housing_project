<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMSAY DTP Dashboard | Housing for All Haryana</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="text-slate-800 min-h-screen flex flex-col">

    <!-- Navbar Header -->
    <header class="bg-slate-900 text-white shadow-md border-b border-orange-500 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/30">
                    <span class="material-symbols-outlined text-orange-500 text-2xl">architecture</span>
                </div>
                <div>
                    <h1 class="text-sm font-black tracking-wider text-white uppercase">MMSAY DTP Panel</h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">District Town Planner Console</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-extrabold text-slate-200">DTP Rohtak Region</p>
                    <p class="text-[9px] text-orange-400 font-black uppercase">MMSAY Scheme Admin</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-orange-500/20 text-orange-400 flex items-center justify-center font-bold text-xs border border-orange-500/40">
                    DT
                </div>
                <a href="{{ route('pp.dtp.login') }}" onclick="event.preventDefault(); document.getElementById('dtp-logout-form').submit();" 
                   class="inline-flex px-3 py-1.5 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 rounded-lg text-[10px] font-black uppercase tracking-wider transition duration-150 items-center gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-xs">logout</span>
                    <span>Logout</span>
                </a>
                <form id="dtp-logout-form" action="{{ route('pp.dtp.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </header>

    <!-- Main Dashboard Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow w-full space-y-6">

        <!-- Top Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-orange-950 rounded-2xl p-4 text-white shadow-sm border border-slate-700/50 flex justify-between items-center">
            <div class="space-y-1">
                <span class="text-[8px] font-black uppercase bg-orange-500 text-white px-2 py-0.5 rounded tracking-widest">DTP CONSOLE</span>
                <h2 class="text-sm font-extrabold uppercase tracking-wider flex items-center gap-1.5">
                    <i class="bi bi-ui-checks"></i> Mukhyamantri Shehri Awas Yojana (MMSAY) Layout Approval Panel
                </h2>
                <p class="text-[10px] text-slate-300 font-light">Monitor layout maps, zoning permits, demarcation plans, and physical possession clearances.</p>
            </div>
            <div class="text-right">
                <span class="text-xs bg-white/10 px-3 py-1.5 rounded-lg border border-white/10 font-mono text-orange-300 text-[10px]">
                    {{ now()->format('d M, Y (h:i A)') }}
                </span>
            </div>
        </div>

        <!-- Row of Grid Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Stat 1 -->
            <div class="bg-white rounded-xl border border-slate-150 p-3 flex items-center gap-3 shadow-sm hover:shadow transition">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">map</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Layout Plans</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">124 Submitted</h3>
                </div>
            </div>
            <!-- Stat 2 -->
            <div class="bg-white rounded-xl border border-slate-150 p-3 flex items-center gap-3 shadow-sm hover:shadow transition">
                <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100 flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">apartment</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Draws Cleared</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">48 Districts</h3>
                </div>
            </div>
            <!-- Stat 3 -->
            <div class="bg-white rounded-xl border border-slate-150 p-3 flex items-center gap-3 shadow-sm hover:shadow transition">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">home_pin</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider font-mono">Plots Demarcated</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">2,450 Plots</h3>
                </div>
            </div>
            <!-- Stat 4 -->
            <div class="bg-white rounded-xl border border-slate-150 p-3 flex items-center gap-3 shadow-sm hover:shadow transition">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">drafts</span>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Possession Issued</p>
                    <h3 class="text-lg font-black text-slate-800 leading-tight">1,840 Signed</h3>
                </div>
            </div>
        </div>

        <!-- Multi-column Layout Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Column 1: Layout Approvals Status Tracker (Left) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Sector Breakdown table -->
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">
                        MMSAY Sector Layout & Approval Status
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[10px]">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase">
                                    <th class="py-2.5 px-3 text-left">Sector / Location</th>
                                    <th class="py-2.5 px-3 text-left">Assigned Developer</th>
                                    <th class="py-2.5 px-3 text-right">Plot Count</th>
                                    <th class="py-2.5 px-3 text-center">Zoning Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-3 font-bold">Sector 12, Gurugram</td>
                                    <td class="py-2.5 px-3">DLF Housing Ltd</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-orange-600">794</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[8px] font-black uppercase rounded-full border border-emerald-100">Approved</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-bold">Sector 23, Faridabad</td>
                                    <td class="py-2.5 px-3">Omaxe Infra Projects</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-orange-600">2,946</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[8px] font-black uppercase rounded-full border border-amber-100">Under Technical Review</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-bold">Sector 5, Rohtak</td>
                                    <td class="py-2.5 px-3">Rohtak Town Developers</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-orange-600">2,709</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[8px] font-black uppercase rounded-full border border-red-100">Clarification Needed</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-bold">Sector 18, Sonipat</td>
                                    <td class="py-2.5 px-3">Emaar Group India</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-orange-600">1,250</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[8px] font-black uppercase rounded-full border border-emerald-100">Approved</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-bold">Sector 9, Panipat</td>
                                    <td class="py-2.5 px-3">L&T Infrastructure</td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-orange-600">1,080</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[8px] font-black uppercase rounded-full border border-emerald-100">Approved</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent DTP Layout Actions -->
                <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">
                        Recent DTP Layout Actions & Notifications
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-start justify-between border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-bold text-slate-700">Gurugram Sector 12 - Final Layout Plan Approved</p>
                                <p class="text-[8.5px] text-slate-400">Action By: <span class="font-bold text-slate-500">Rohtak DTP Office</span></p>
                            </div>
                            <div class="text-right text-[8.5px] font-mono text-slate-400">2 hours ago</div>
                        </div>
                        <div class="flex items-start justify-between border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-bold text-slate-700">Faridabad Sector 23 - Request clarification on green area ratio</p>
                                <p class="text-[8.5px] text-slate-400">Action By: <span class="font-bold text-slate-500">Rohtak DTP Office</span></p>
                            </div>
                            <div class="text-right text-[8.5px] font-mono text-slate-400">5 hours ago</div>
                        </div>
                        <div class="flex items-start justify-between border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-bold text-slate-700">Sonipat Sector 18 - Demarcation plan verified and signed</p>
                                <p class="text-[8.5px] text-slate-400">Action By: <span class="font-bold text-slate-500">Rohtak DTP Office</span></p>
                            </div>
                            <div class="text-right text-[8.5px] font-mono text-slate-400">1 day ago</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Column 3: Quick Action Panel (Right) -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">
                        DTP Quick Actions Console
                    </h4>
                    <p class="text-[10px] text-slate-400 mb-4">Select operations to verify or manage layout approvals.</p>
                </div>
                
                <div class="space-y-3 my-auto">
                    <button class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-sm">add_circle</span> Approve New Layout
                    </button>
                    <button class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-wider transition flex items-center justify-center gap-1.5 cursor-pointer border border-slate-200">
                        <span class="material-symbols-outlined text-sm">cloud_upload</span> Upload Site Plans
                    </button>
                    <button class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-wider transition flex items-center justify-center gap-1.5 cursor-pointer border border-slate-200">
                        <span class="material-symbols-outlined text-sm">description</span> Issue Possession Letter
                    </button>
                </div>

                <div class="border-t border-slate-100 pt-3 mt-4 text-center">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">MMSAY Scheme Planning</span>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-center py-4 border-t border-slate-800 text-[10px] mt-auto">
        &copy; {{ now()->format('Y') }} Department of Housing For All, Government of Haryana. All rights reserved.
    </footer>

</body>
</html>
