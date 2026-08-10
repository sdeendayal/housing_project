<?php $__env->startSection('title', 'MMGAY BDPO Dashboard'); ?>
<?php $__env->startSection('page_header', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<main class="ml-[260px] min-h-screen bg-[#f3f6fc] p-4 flex-1">
    <!-- Spacer to clear fixed top navbar -->
    <div style="height: 80px;" class="w-full shrink-0"></div>


    <!-- Header Banner - Denser & Modern Gradient -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#0f2027] via-[#203a43] to-[#2c5364] shadow-md mb-4 py-4 px-6 border border-slate-700/10">
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        <div class="relative flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-xl">location_on</span>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight">
                        <?php echo e(strtoupper($bdo->block_name ?? $bdo->district_name ?? 'Haryana')); ?> Block BDPO Panel
                    </h2>
                    <p class="text-[10px] text-slate-300 font-semibold uppercase mt-0.5">Block Development & Panchayat Officer • Mukhyamantri Gramin Awas Yojana</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/15 rounded-lg px-3 py-1.5 shadow-sm text-xs font-bold">
                <span class="material-symbols-outlined text-sm">calendar_month</span>
                <span><?php echo e(now()->format('d M Y')); ?></span>
            </div>
        </div>
    </div>

    <!-- Stats Row - Tighter & More Densely Aligned -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2.5 mb-4">
        <!-- Total Eligible -->
        <a href="<?php echo e(route('mmgay.bdo.eligibility-list', ['all' => 1])); ?>" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-slate-300 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Total Eligible</p>
                <h2 class="text-lg font-extrabold text-slate-700 mt-0.5"><?php echo e($stats['total_eligible']); ?></h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-slate-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-slate-600 text-base">groups</span>
            </div>
        </a>

        <!-- Schedule Pending -->
        <a href="<?php echo e(route('mmgay.bdo.eligibility-list')); ?>" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-blue-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider truncate">Schedule Pending</p>
                <h2 class="text-lg font-extrabold text-blue-700 mt-0.5"><?php echo e($stats['not_scheduled']); ?></h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-700 text-base">pending_actions</span>
            </div>
        </a>

        <!-- Citizen Confirmation Pending -->
        <a href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Visit Scheduled'])); ?>" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-orange-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider">Confirmation Pending From Citizen</p>
                <h2 class="text-lg font-extrabold text-orange-700 mt-0.5"><?php echo e($stats['awaiting_citizen']); ?></h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-orange-700 text-base">contact_support</span>
            </div>
        </a>

        <!-- Field Visit Pending -->
        <a href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Slot Selected'])); ?>" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-indigo-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider">Physical/Site Visit Pending</p>
                <h2 class="text-lg font-extrabold text-indigo-700 mt-0.5"><?php echo e($stats['awaiting_coordinates']); ?></h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-indigo-700 text-base">location_on</span>
            </div>
        </a>

        <!-- E-Possession Pending -->
        <a href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Site Verified'])); ?>" class="bg-white rounded-xl shadow-sm border border-slate-150 p-3 flex items-center justify-between hover:shadow hover:border-amber-200 transition font-semibold">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider">Registry documentation, verify</p>
                <h2 class="text-lg font-extrabold text-amber-700 mt-0.5"><?php echo e($stats['awaiting_bdo_doc']); ?></h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-amber-700 text-base">description</span>
            </div>
        </a>

        <!-- Verified -->
        <a href="<?php echo e(route('mmgay.bdo.possession-applications', ['status' => 'Verified'])); ?>" class="bg-white rounded-xl shadow-sm border border-slate-100 p-3 flex items-center justify-between hover:shadow hover:border-emerald-200 transition">
            <div class="min-w-0">
                <p class="text-[9px] uppercase text-slate-400 font-bold tracking-wider">Possession given</p>
                <h2 class="text-lg font-extrabold text-emerald-700 mt-0.5"><?php echo e($stats['verified']); ?></h2>
            </div>
            <div class="w-8.5 h-8.5 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-700 text-base">verified</span>
            </div>
        </a>
    </div>

    <!-- Phase Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-3.5 flex items-center gap-2 mb-4 shrink-0">
        <span class="text-xs font-black uppercase text-slate-400 tracking-wider mr-4">Select Phase:</span>
        <div class="flex gap-2">
            <?php $__currentLoopData = $phases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $phaseVal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('mmgay.bdo.dashboard')); ?>?phase=<?php echo e($phaseVal); ?>" 
                   class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-lg transition-all 
                   <?php echo e($selectedPhase == $phaseVal ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'); ?>">
                    Phase <?php echo e($phaseVal); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Drill-Down Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 flex-1">
        
        <!-- Column 1: Villages List -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col h-[calc(100vh-310px)] min-h-[420px] overflow-hidden">
            <div class="pb-3 border-b border-slate-100 mb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-blue-600 text-lg">domain</span>
                        Villages (Phase <?php echo e($selectedPhase); ?>)
                    </h3>
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Select a village to view beneficiaries</p>
                </div>
                <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded">
                    Total: <?php echo e($villages->count()); ?>

                </span>
            </div>

            <div class="flex-1 overflow-y-auto space-y-1.5 pr-1">
                <?php $__empty_1 = true; $__currentLoopData = $villages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between p-3 rounded-lg border transition-all 
                       <?php echo e($selectedVillageId == $vil->VillageId ? 'bg-blue-50 border-blue-200 text-blue-800 font-bold' : 'bg-slate-50 border-slate-150 text-slate-700 hover:bg-slate-100/70'); ?>">
                        <a href="<?php echo e(route('mmgay.bdo.dashboard')); ?>?phase=<?php echo e($selectedPhase); ?>&village_id=<?php echo e($vil->VillageId); ?>" class="flex items-center gap-2 flex-1 min-w-0">
                            <span class="material-symbols-outlined text-base <?php echo e($selectedVillageId == $vil->VillageId ? 'text-blue-600' : 'text-slate-400'); ?>">map</span>
                            <span class="text-xs uppercase tracking-wide truncate"><?php echo e($vil->VillageName); ?></span>
                        </a>
                        <div class="flex items-center gap-2 shrink-0">
                            <?php if(!empty($vil->map_pdf)): ?>
                                <a href="<?php echo e(asset('phase1_plans_gps_map/' . $vil->map_pdf)); ?>" target="_blank" 
                                   class="inline-flex items-center gap-0.5 px-1 py-0.5 text-[8px] font-black text-red-650 hover:text-red-755 bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-all" 
                                   title="Open Village GPS Map PDF">
                                    <span class="material-symbols-outlined text-[10px] font-black">picture_as_pdf</span>
                                    <span>MAP</span>
                                </a>
                            <?php else: ?>
                                <span class="inline-flex items-center px-1 py-0.5 text-[7px] font-semibold text-slate-450 bg-slate-50 border border-slate-200 rounded" title="Map not available">
                                    MAP N/A
                                </span>
                            <?php endif; ?>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full <?php echo e($selectedVillageId == $vil->VillageId ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600'); ?>">
                                <?php echo e($vil->total_beneficiaries); ?>

                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="py-12 text-center text-slate-400 font-semibold text-xs">
                        No villages found with entries in Phase <?php echo e($selectedPhase); ?>.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Column 2 & 3: Beneficiaries & Details -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-4 flex flex-col h-[calc(100vh-310px)] min-h-[420px] overflow-hidden">
            <?php if(!$selectedVillageId): ?>
                <div class="flex-1 flex flex-col items-center justify-center text-center p-8">
                    <span class="material-symbols-outlined text-slate-300 text-5xl mb-3">supervisor_account</span>
                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wide">No Village Selected</h4>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase max-w-xs font-semibold">Please select a village from the left sidebar to display registered beneficiaries.</p>
                </div>
            <?php else: ?>
                <div class="pb-3 border-b border-slate-100 mb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-blue-600 text-lg">group</span>
                            <span>Beneficiaries: <?php echo e($selectedVillageName); ?></span>
                            <?php if(!empty($selectedVillagePdf)): ?>
                                <a href="<?php echo e(asset('phase1_plans_gps_map/' . $selectedVillagePdf)); ?>" target="_blank" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-[9px] font-black text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-all ml-3 shadow-sm hover:shadow" 
                                   title="Open Village GPS Map PDF">
                                    <span class="material-symbols-outlined text-xs">picture_as_pdf</span>
                                    <span>VILLAGE GPS MAP</span>
                                </a>
                            <?php endif; ?>
                        </h3>
                        <p class="text-[9px] text-slate-400 uppercase font-semibold">Click a beneficiary to toggle detail drawer</p>
                    </div>
                    <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                        Phase <?php echo e($selectedPhase); ?> | <?php echo e($beneficiaries->count()); ?> Records
                    </span>
                </div>

                <!-- Search bar for Beneficiaries -->
                <div class="mb-4 pb-3 border-b border-slate-100">
                    <form action="<?php echo e(route('mmgay.bdo.dashboard')); ?>" method="GET" class="flex gap-2">
                        <input type="hidden" name="phase" value="<?php echo e($selectedPhase); ?>">
                        <input type="hidden" name="village_id" value="<?php echo e($selectedVillageId); ?>">
                        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search beneficiary name, mobile, reg..." class="border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500 flex-1">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] font-bold">search</span> Filter
                        </button>
                        <?php if($search): ?>
                            <a href="<?php echo e(route('mmgay.bdo.dashboard')); ?>?phase=<?php echo e($selectedPhase); ?>&village_id=<?php echo e($selectedVillageId); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="w-full flex-grow overflow-y-auto overflow-x-auto pr-1">
                    <table class="w-full text-xs min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase text-[9px] font-bold border-b border-slate-100">
                                <th class="px-3 py-2 text-left">Sr.No.</th>
                                <th class="px-3 py-2 text-left">Registration ID</th>
                                <th class="px-3 py-2 text-left">Beneficiary Name</th>
                                <th class="px-3 py-2 text-left">Father's Name</th>
                                <th class="px-3 py-2 text-left">Mobile No</th>
                                <th class="px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ben): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-3 py-1.5 font-bold text-slate-400">
                                        <?php echo e($loop->iteration + ($beneficiaries->currentPage() - 1) * $beneficiaries->perPage()); ?>

                                    </td>
                                    <td class="px-3 py-1.5 font-mono font-bold text-slate-800">
                                        <?php echo e($ben->RegistrationNo); ?>

                                    </td>
                                    <td class="px-3 py-1.5 font-semibold text-slate-800">
                                        <?php echo e($ben->OwnerName); ?>

                                    </td>
                                    <td class="px-3 py-1.5 text-slate-500">
                                        <?php echo e($ben->FatherHusbandName ?: '—'); ?>

                                    </td>
                                    <td class="px-3 py-1.5 font-mono text-slate-500 text-[11px]">
                                        <?php echo e($ben->MobileNo); ?>

                                    </td>
                                    <td class="px-3 py-1.5 text-center">
                                        <button onclick="toggleDetail(<?php echo e($ben->OwnerId); ?>)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-black px-2 py-1 rounded text-[10px] uppercase shadow-sm border border-slate-200 inline-flex items-center gap-1 transition-all">
                                            <span class="material-symbols-outlined text-[12px]">visibility</span>
                                            <span>View</span>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Collapsible Detailed Row -->
                                <tr id="detail-<?php echo e($ben->OwnerId); ?>" class="hidden bg-slate-50/50">
                                    <td colspan="6" class="p-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-[11px]">
                                            <div class="space-y-2">
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">Possession Status</span>
                                                    <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wide
                                                        <?php if($ben->possession_status === 'Verified'): ?> bg-emerald-50 text-emerald-700 border border-emerald-100
                                                        <?php elseif($ben->possession_status === 'Visit Scheduled'): ?> bg-orange-50 text-orange-700 border border-orange-100
                                                        <?php elseif($ben->possession_status === 'Slot Selected'): ?> bg-indigo-50 text-indigo-700 border border-indigo-100
                                                        <?php else: ?> bg-blue-50 text-blue-700 border border-blue-100
                                                        <?php endif; ?>">
                                                        <?php echo e($ben->possession_status); ?>

                                                    </span>
                                                </div>
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">PPP / Family ID</span>
                                                    <span class="font-mono font-bold text-slate-700"><?php echo e($ben->PPPId ?: '—'); ?></span>
                                                </div>
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">Member ID</span>
                                                    <span class="font-mono font-bold text-slate-700"><?php echo e($ben->MemberId ?: '—'); ?></span>
                                                </div>
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">Address Detail</span>
                                                    <span class="font-medium text-slate-605 uppercase"><?php echo e($ben->OwnerAddress ?: '—'); ?></span>
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">Possession App Number</span>
                                                    <span class="font-mono font-bold text-slate-700"><?php echo e($ben->application_number ?: 'Awaiting initialization'); ?></span>
                                                </div>
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">Village/Block</span>
                                                    <span class="font-bold text-slate-700 uppercase"><?php echo e($ben->VillageName); ?> / <?php echo e($ben->BlockName); ?></span>
                                                </div>
                                                <div class="flex justify-between border-b border-slate-200/50 pb-1">
                                                    <span class="text-slate-400 uppercase font-black tracking-wider text-[8px]">District</span>
                                                    <span class="font-bold text-slate-700 uppercase"><?php echo e($ben->DistrictName); ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="md:col-span-2 pt-2 border-t border-slate-200 flex justify-end gap-2 bg-white/50 p-2 rounded-lg">
                                                <?php if($ben->possession_status === 'Eligible for Physical Possession'): ?>
                                                    <a href="<?php echo e(route('mmgay.bdo.schedule-form', $ben->secure_id)); ?>" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-3 py-1.5 rounded font-black uppercase transition shadow-sm">
                                                        <span class="material-symbols-outlined text-[13px] font-bold">calendar_month</span> Schedule Visit
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('mmgay.bdo.verify-form', $ben->secure_id)); ?>" class="inline-flex items-center gap-1 bg-slate-200 hover:bg-slate-300 text-slate-700 text-[10px] px-3 py-1.5 rounded font-black uppercase transition border border-slate-300">
                                                        <span class="material-symbols-outlined text-[13px]">visibility</span> View Details
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-slate-400 font-semibold">
                                        No beneficiaries found in <?php echo e($selectedVillageName); ?> for Phase <?php echo e($selectedPhase); ?>.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="mt-4 pt-3 border-t border-slate-100 shrink-0">
                    <?php echo e($beneficiaries->links('partials.compact-pagination')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    function toggleDetail(id) {
        const detailDiv = document.getElementById(`detail-${id}`);
        if (detailDiv.classList.contains('hidden')) {
            detailDiv.classList.remove('hidden');
        } else {
            detailDiv.classList.add('hidden');
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmgayBdoAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\sports\housing_project\resources\views/mmgay/bdo/dashboard.blade.php ENDPATH**/ ?>