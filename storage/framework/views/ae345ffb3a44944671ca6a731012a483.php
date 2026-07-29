<?php $__env->startSection('title', 'MMSAY Department Dashboard'); ?>
<?php $__env->startSection('content'); ?>
    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">
            <!-- Breadcrumbs -->
            <div class="flex items-center justify-between mb-4">

                <!-- Breadcrumb -->
                <nav aria-label="Breadcrumb" class="flex">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a class="inline-flex items-center text-sm font-medium text-on-surface-variant hover:text-primary"
                                href="<?php echo e(route('mmsay.dashboard')); ?>">
                                <span class="material-symbols-outlined text-sm mr-2">home</span>
                                Dashboard
                            </a>
                        </li>

                        <li aria-current="page">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined text-on-surface-variant">
                                    chevron_right
                                </span>
                                <span class="ml-1 text-sm font-medium text-primary md:ml-2">
                                    Add Site Engineer
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- View List Button -->
                <a href="<?php echo e(route('mmsay.officers.list')); ?>"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">

                    <span class="material-symbols-outlined text-sm">
                        visibility
                    </span>

                    View Officers
                </a>

            </div>

            <!-- Form Container -->
            <div class="glass-card rounded-xl overflow-hidden border border-outline-variant bg-surface-container-lowest">
                <div class="bg-primary-container px-8 py-4">
                    <h3 class="text-on-primary-container font-label-md font-bold uppercase tracking-wider">Officer Details
                        &amp; Regional Assignment</h3>
                </div>

                <form method="POST" action="<?php echo e(route('officers.store')); ?>" class="p-8 space-y-6">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Officer Name -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="officer-name">Officer Name
                                <span class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="person">person</span>
                                <input
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md custom-input-focus transition-all bg-surface-container-low/50"
                                    name="officer_name" value="<?php echo e(old('officer_name')); ?>" required
                                    placeholder="Full Legal Name" required="" type="text" />
                                <?php $__errorArgs = ['officer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-xs text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <p class="text-xs text-on-surface-variant">As per government identification records.</p>
                        </div>
                        <!-- District Name -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="district">District Assignment
                                <span class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="location_on">location_on</span>
                                <select name="district_id" id="district"
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md bg-surface-container-low/50"
                                    required>

                                    <option value="">Select District</option>

                                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($district->DistrictId); ?>"
                                            <?php echo e(old('district_id') == $district->DistrictId ? 'selected' : ''); ?>>
                                            <?php echo e($district->DistrictName); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </select>

                                <?php $__errorArgs = ['district_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-xs text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <span
                                    class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none"
                                    data-icon="keyboard_arrow_down">keyboard_arrow_down</span>
                            </div>
                        </div>
                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="email">Email Address <span
                                    class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="mail">mail</span>
                                <input
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md custom-input-focus transition-all bg-surface-container-low/50"
                                    name="email" value="<?php echo e(old('email')); ?>" required placeholder="officer@haryana.gov.in"
                                    type="email" />
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-xs text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <!-- Mobile Number -->
                        <div class="space-y-2">
                            <label class="block font-label-md font-bold text-on-surface" for="mobile">Mobile Number <span
                                    class="text-error">*</span></label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                    data-icon="smartphone">smartphone</span>
                                <input
                                    class="w-full pl-10 pr-4 py-3 border border-outline-variant rounded-lg font-body-md custom-input-focus transition-all bg-surface-container-low/50"
                                    name="mobile" value="<?php echo e(old('mobile')); ?>" required placeholder="+91 00000-00000"
                                    type="tel" />
                                <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-xs text-red-500"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                    <!-- Additional Details section with high density UI -->
                    
                    <!-- Footer Actions -->
                    <div class="flex items-center justify-end gap-4 pt-8 mt-4 border-t border-outline-variant">
                        <button
                            class="px-6 py-2.5 text-on-surface-variant hover:text-primary font-bold transition-all hover:bg-surface-container rounded-lg"
                            type="button">
                            Cancel
                        </button>
                        <button
                            class="px-8 py-2.5 bg-primary text-white font-bold rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.98] transition-all flex items-center gap-2"
                            type="submit">
                            <span class="material-symbols-outlined text-sm" data-icon="person_add">person_add</span>
                            Save Officer
                        </button>
                    </div>
                </form>
            </div>
            <!-- Contextual Help Card -->
            
        </div>
    </main>
    <!-- Success Notification (Hidden by default) -->
    <div class="fixed bottom-8 right-8 bg-surface-container-highest border border-outline shadow-2xl rounded-xl p-4 flex items-center gap-4 translate-y-24 opacity-0 transition-all duration-500 z-[100]"
        id="toast">
        <div
            class="w-10 h-10 bg-tertiary-container text-on-tertiary-container rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined" data-icon="check_circle"
                style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <div>
            <p class="font-bold text-on-surface">Officer Added Successfully</p>
            <p class="text-sm text-on-surface-variant">Profile sync in progress.</p>
        </div>
    </div>
    <!-- Success Notification -->
    <div id="successToast"
        class="fixed top-5 right-5 bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform -translate-y-24 opacity-0 transition-all duration-500 z-[9999]">

        <span class="material-symbols-outlined">check_circle</span>

        <div>
            <p class="font-semibold">Success</p>
            <p class="text-sm"><?php echo e(session('success')); ?></p>
        </div>
    </div>

    <!-- Error Notification -->
    <div id="errorToast"
        class="fixed top-5 right-5 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform -translate-y-24 opacity-0 transition-all duration-500 z-[9999]">

        <span class="material-symbols-outlined">error</span>

        <div>
            <p class="font-semibold">Error</p>
            <p class="text-sm"><?php echo e(session('error')); ?></p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            <?php if(session('success')): ?>
                const successToast = document.getElementById('successToast');

                successToast.classList.remove('-translate-y-24', 'opacity-0');
                successToast.classList.add('translate-y-0', 'opacity-100');

                setTimeout(() => {
                    successToast.classList.remove('translate-y-0', 'opacity-100');
                    successToast.classList.add('-translate-y-24', 'opacity-0');
                }, 4000);
            <?php endif; ?>

            <?php if(session('error')): ?>
                const errorToast = document.getElementById('errorToast');

                errorToast.classList.remove('-translate-y-24', 'opacity-0');
                errorToast.classList.add('translate-y-0', 'opacity-100');

                setTimeout(() => {
                    errorToast.classList.remove('translate-y-0', 'opacity-100');
                    errorToast.classList.add('-translate-y-24', 'opacity-0');
                }, 4000);
            <?php endif; ?>

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentAddDistrictOfficer.blade.php ENDPATH**/ ?>