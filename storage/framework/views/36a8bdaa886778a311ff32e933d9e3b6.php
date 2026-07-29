<?php $__env->startSection('title', 'BDO Profile & Settings'); ?>
<?php $__env->startSection('page_header', 'Profile'); ?>

<?php $__env->startSection('content'); ?>
<main class="ml-[260px] mt-14 min-h-screen bg-[#f3f6fc] p-4 flex flex-col gap-4">

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#0f2027] via-[#203a43] to-[#2c5364] shadow-md py-4 px-6 border border-slate-700/10">
        <div class="absolute -right-20 -top-20 w-60 h-60 bg-white/5 rounded-full blur-3xl"></div>
        <div class="relative flex items-center justify-between text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                    <span class="material-symbols-outlined text-white text-xl">account_circle</span>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight">My Profile & Settings</h2>
                    <p class="text-[10px] text-slate-300 font-semibold uppercase mt-0.5">View your account profile information and manage your login password</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/15 rounded-lg px-3 py-1.5 shadow-sm text-xs font-bold">
                <span class="material-symbols-outlined text-sm">shield</span>
                <span>Secure Panel</span>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 flex-grow items-start">
        
        <!-- Left Side: Profile Details (Readonly) -->
        <div class="lg:col-span-7 bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <div class="pb-3 border-b border-slate-100 mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-blue-600 text-lg">badge</span>
                        Officer Profile Details
                    </h3>
                    <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Authorized account details</p>
                </div>
                <span class="flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-800 text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded">
                    <span class="material-symbols-outlined text-[10px]" style="font-variation-settings: 'FILL' 1;">lock</span>
                    Locked
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Full Name</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">person</span>
                        <span class="text-xs font-bold"><?php echo e($bdo->name); ?></span>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Email Address</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">mail</span>
                        <span class="text-xs font-bold"><?php echo e($bdo->email); ?></span>
                    </div>
                </div>

                <!-- Mobile -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Mobile Number</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">phone</span>
                        <span class="text-xs font-bold"><?php echo e($bdo->mobile ?? 'Not Provided'); ?></span>
                    </div>
                </div>

                <!-- Designation / Role -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Designation</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">work</span>
                        <span class="text-xs font-bold">Block Development & Panchayat Officer (BDPO)</span>
                    </div>
                </div>

                <!-- District Name -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">District</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">location_city</span>
                        <span class="text-xs font-bold"><?php echo e($bdo->district_name ?? 'Haryana'); ?></span>
                    </div>
                </div>

                <!-- Block Name -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Block</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">map</span>
                        <span class="text-xs font-bold"><?php echo e($bdo->block_name ?? 'ALL BLOCKS'); ?></span>
                    </div>
                </div>

                <!-- Scheme -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Scheme</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-500 select-none">
                        <span class="material-symbols-outlined text-base text-slate-400">home</span>
                        <span class="text-xs font-bold"><?php echo e($bdo->scheme ?? 'MMGAY'); ?></span>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Account Status</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-emerald-600 select-none">
                        <span class="material-symbols-outlined text-base text-emerald-500" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="text-xs font-bold uppercase tracking-wider">Active</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 p-3 rounded-lg bg-amber-50/50 border border-amber-100 text-[10px] font-medium text-amber-800 flex items-start gap-2">
                <span class="material-symbols-outlined text-base text-amber-600 shrink-0">info</span>
                <span>To modify any details above, please contact the State Nodal Officer or Portal Administrator. Only the login password can be updated directly from this panel.</span>
            </div>
        </div>

        <!-- Right Side: Change Password Form (Editable) -->
        <div class="lg:col-span-5 bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col">
            <div class="pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-blue-600 text-lg">lock_reset</span>
                    Change Password
                </h3>
                <p class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Change your account login password</p>
            </div>

            <form action="<?php echo e(route('mmgay.bdo.profile.change-password')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <!-- Current Password -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">Current Password <span class="text-red-500">*</span></label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined text-base text-slate-400 absolute left-3 select-none">lock</span>
                        <input type="password" name="current_password" required
                               class="w-full pl-9 pr-10 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium"
                               placeholder="••••••••">
                        <button type="button" class="absolute right-3 text-slate-400 hover:text-slate-600 toggle-password-visibility">
                            <span class="material-symbols-outlined text-base">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">New Password <span class="text-red-500">*</span></label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined text-base text-slate-400 absolute left-3 select-none">lock_open</span>
                        <input type="password" name="new_password" required
                               class="w-full pl-9 pr-10 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium"
                               placeholder="Min 6 characters">
                        <button type="button" class="absolute right-3 text-slate-400 hover:text-slate-600 toggle-password-visibility">
                            <span class="material-symbols-outlined text-base">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 tracking-wider mb-1">Confirm New Password <span class="text-red-500">*</span></label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined text-base text-slate-400 absolute left-3 select-none">lock_open</span>
                        <input type="password" name="new_password_confirmation" required
                               class="w-full pl-9 pr-10 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium"
                               placeholder="Re-enter new password">
                        <button type="button" class="absolute right-3 text-slate-400 hover:text-slate-600 toggle-password-visibility">
                            <span class="material-symbols-outlined text-base">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 bg-[#0058bc] hover:bg-[#004493] text-white text-xs font-extrabold uppercase tracking-wide py-2.5 px-4 rounded-lg shadow-sm transition-all duration-150">
                    <span class="material-symbols-outlined text-base">save</span>
                    Update Password
                </button>
            </form>
        </div>

    </div>

</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password visibility functionality
        const toggleButtons = document.querySelectorAll('.toggle-password-visibility');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const input = this.closest('div').querySelector('input');
                const icon = this.querySelector('.material-symbols-outlined');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    icon.textContent = 'visibility';
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmgayBdoAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\sports\housing_project\resources\views/mmgay/bdo/profile.blade.php ENDPATH**/ ?>