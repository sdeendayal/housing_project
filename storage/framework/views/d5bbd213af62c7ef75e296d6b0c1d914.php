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
                                    Officers List
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Site Engineers</h2>
                        <p class="text-sm text-slate-500">
                            Total Officers: <?php echo e($officers->count()); ?>

                        </p>
                    </div>

                    <a href="<?php echo e(route('mmsay-department-add-district-officer')); ?>"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg shadow hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Officer
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">#</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Officer
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">District
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Email
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Mobile
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Status
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase">Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <?php $__empty_1 = true; $__currentLoopData = $officers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $officer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-50 transition">

                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        <?php echo e($key + 1); ?>

                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">

                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                                                <?php echo e(strtoupper(substr($officer->name, 0, 1))); ?>

                                            </div>

                                            <div>
                                                <p class="font-semibold text-slate-800">
                                                    <?php echo e($officer->name); ?>

                                                </p>

                                                <p class="text-xs text-slate-500">
                                                    Officer ID: <?php echo e($officer->id); ?>

                                                </p>
                                            </div>

                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?php echo e($officer->DistrictName); ?>

                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?php echo e($officer->email); ?>

                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?php echo e($officer->mobile); ?>

                                    </td>

                                    <td class="px-6 py-4">
                                        <?php if($officer->Is_Active == 1): ?>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">

                                            <!-- Edit -->
                                            <button
                                                onclick="openEditModal(
'<?php echo e($officer->id); ?>',
'<?php echo e($officer->name); ?>',
'<?php echo e($officer->email); ?>',
'<?php echo e($officer->mobile); ?>'
)"
                                                class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition">

                                                <span class="material-symbols-outlined text-[20px]">
                                                    edit
                                                </span>

                                            </button>
                                            <!-- Transfer -->
                                            <button
                                                onclick="openTransferModal(
'<?php echo e($officer->id); ?>',
'<?php echo e($officer->name); ?>',
'<?php echo e($officer->district_id); ?>'
)"
                                                class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition">

                                                <span class="material-symbols-outlined text-[20px]">
                                                    swap_horiz
                                                </span>

                                            </button>

                                            <!-- Delete -->
                                            <button onclick="deleteOfficer(<?php echo e($officer->id); ?>)"
                                                class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition">

                                                <span class="material-symbols-outlined text-[20px]">
                                                    delete
                                                </span>

                                            </button>

                                        </div>
                                    </td>

                                </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                <tr>
                                    <td colspan="7" class="py-12 text-center">

                                        <div class="flex flex-col items-center">

                                            <span class="material-symbols-outlined text-6xl text-slate-300 mb-2">
                                                group_off
                                            </span>

                                            <h3 class="font-semibold text-slate-700">
                                                No Officers Found
                                            </h3>

                                            <p class="text-sm text-slate-500">
                                                Add your first site engineer.
                                            </p>

                                        </div>

                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="editModal" onclick="if(event.target===this) closeEditModal()"
            class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">

                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-slate-800">
                        Edit Site Engineer
                    </h3>

                    <button onclick="closeEditModal()">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form id="editOfficerForm" class="p-6">
                    <?php echo csrf_field(); ?>

                    <input type="hidden" id="edit_user_id">

                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Officer Name
                            </label>
                            <input type="text" id="edit_name"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Enter Officer Name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Email Address
                            </label>
                            <input type="email" id="edit_email"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Enter Email">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Mobile Number
                            </label>
                            <input type="text" id="edit_mobile" maxlength="10"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                placeholder="Enter Mobile Number">
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">

                        <button type="button" onclick="closeEditModal()"
                            class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                            Cancel
                        </button>

                        <button type="submit" id="updateBtn"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-2">

                            <span class="material-symbols-outlined text-sm">
                                save
                            </span>

                            Update Officer
                        </button>

                    </div>
                </form>

            </div>

        </div>
        <div id="transferModal" onclick="if(event.target===this) closeTransferModal()"
            class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">

                <div class="flex items-center justify-between px-6 py-4 border-b">

                    <h3 class="text-lg font-semibold">
                        Transfer Site Engineer
                    </h3>

                    <button onclick="closeTransferModal()">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                </div>

                <form id="transferOfficerForm" class="p-6">

                    <?php echo csrf_field(); ?>

                    <input type="hidden" id="transfer_user_id">

                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Officer Name
                            </label>

                            <input type="text" id="transfer_officer_name" readonly
                                class="w-full px-4 py-3 bg-slate-100 border rounded-xl">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Transfer To District
                            </label>

                            <select id="transfer_district_id" class="w-full px-4 py-3 border rounded-xl">

                                <option value="">
                                    Select District
                                </option>

                                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($district->DistrictId); ?>">
                                        <?php echo e($district->DistrictName); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-6">

                        <button type="button" onclick="closeTransferModal()" class="px-5 py-2 border rounded-xl">
                            Cancel
                        </button>

                        <button type="submit" id="transferBtn" class="px-5 py-2 bg-amber-600 text-white rounded-xl">

                            Transfer
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Success Notification (Hidden by default) -->
    <script>
        function openEditModal(id, name, email, mobile) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_mobile').value = mobile;

            const modal = document.getElementById('editModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');

            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
    <script>
        document.getElementById('editOfficerForm').addEventListener('submit', function(e) {

            e.preventDefault();

            const btn = document.getElementById('updateBtn');

            btn.disabled = true;
            btn.innerHTML = `
        <span class="material-symbols-outlined animate-spin">
            progress_activity
        </span>
        Updating...
    `;

            fetch("<?php echo e(route('mmsay.officer.update')); ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                    },
                    body: JSON.stringify({
                        user_id: document.getElementById('edit_user_id').value,
                        name: document.getElementById('edit_name').value,
                        email: document.getElementById('edit_email').value,
                        mobile: document.getElementById('edit_mobile').value
                    })
                })
                .then(response => response.json())
                .then(data => {

                    btn.disabled = false;
                    btn.innerHTML = `
<span class="material-symbols-outlined text-sm">save</span>
Update Officer
`;

                    if (data.status) {

                        closeEditModal();

                        Swal.fire({
                            icon: 'success',
                            title: 'Updated Successfully',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });

                    }

                })
                .catch(error => {
                    btn.disabled = false;
                    btn.innerHTML = `
<span class="material-symbols-outlined text-sm">save</span>
Update Officer
`;

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong.'
                    });
                });

        });
    </script>
    <script>
        function openTransferModal(id, name, districtId) {
            document.getElementById('transfer_user_id').value = id;
            document.getElementById('transfer_officer_name').value = name;
            document.getElementById('transfer_district_id').value = districtId;

            document.getElementById('transferModal')
                .classList.remove('hidden');

            document.getElementById('transferModal')
                .classList.add('flex');
        }

        function closeTransferModal() {
            document.getElementById('transferModal')
                .classList.remove('flex');

            document.getElementById('transferModal')
                .classList.add('hidden');
        }
    </script>
    <script>
        document.getElementById('transferOfficerForm')
            .addEventListener('submit', function(e) {

                e.preventDefault();

                const btn = document.getElementById('transferBtn');

                btn.disabled = true;
                btn.innerHTML = 'Transferring...';

                fetch("<?php echo e(route('mmsay.officer.transfer')); ?>", {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                        },

                        body: JSON.stringify({

                            user_id: document.getElementById('transfer_user_id').value,

                            district_id: document.getElementById('transfer_district_id').value
                        })

                    })

                    .then(response => response.json())

                    .then(data => {

                        btn.disabled = false;
                        btn.innerHTML = 'Transfer';

                        if (data.status) {
                            closeTransferModal();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }

                    });

            });
    </script>

    <script>
        function deleteOfficer(id) {
            Swal.fire({
                title: 'Delete Officer?',
                text: 'This officer will be removed from active list.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {
                    fetch("<?php echo e(route('mmsay.officer.delete')); ?>", {

                            method: "POST",

                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                            },

                            body: JSON.stringify({
                                user_id: id
                            })

                        })
                        .then(response => response.json())
                        .then(data => {

                            if (data.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted Successfully',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }

                        })
                        .catch(error => {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong.'
                            });

                        });
                }

            });
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/officersList.blade.php ENDPATH**/ ?>