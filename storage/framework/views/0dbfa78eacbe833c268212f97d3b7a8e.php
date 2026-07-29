<?php $__env->startSection('title', 'MMSAY Department Dashboard'); ?>
<?php $__env->startSection('content'); ?>

    <main class="ml-52 pt-20 px-5 pb-5 min-h-screen">
        <div class="max-w-container-max mx-auto space-y-md">

            <!-- END: PageHeader -->
            <!-- BEGIN: MainContentContainer -->
            <main class="bg-white rounded-custom border border-gray-200 shadow-sm overflow-hidden">
                <!-- BEGIN: PropertyDetailsHeader -->
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-3">
                    <h2 class="text-sm font-bold text-black-600 uppercase tracking-wide  border-blue-100 pb-2">Property EMI
                        Details</h2>
                </div>
                <!-- END: PropertyDetailsHeader -->
                <div class="p-6 space-y-8">
                    <!-- BEGIN: LocationDetailsSection -->
                    <section data-purpose="location-details">
                        <div class="mb-4">
                            <h3
                                class="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-blue-100 pb-2">
                                Location Details</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    District Office
                                </label>

                                <select id="district_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select District</option>

                                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($district->DistrictId); ?>">
                                            <?php echo e($district->DistrictName); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    City Office
                                </label>

                                <select id="city_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="">Select City</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sector
                                </label>

                                <select id="sector_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="">Select Sector</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Asset Number
                                </label>

                                <select id="asset_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="">Select Asset</option>
                                </select>
                            </div>

                        </div>
                    </section>
                    <!-- END: LocationDetailsSection -->
                    <!-- BEGIN: PaymentDetailsSection -->
                    <section data-purpose="payment-details">
                        <div class="mb-4 flex justify-between items-center">
                            <h3
                                class="text-sm font-bold text-blue-600 uppercase tracking-wide border-b border-blue-100 pb-2 flex-grow">
                                Payment Details</h3>

                        </div>
                        <div class="bg-white border rounded-xl p-5 mt-6">

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Total Cost
                                    </label>

                                    <input type="text" id="flat_cost" readonly
                                        class="w-full bg-gray-50 border rounded-lg px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Registration Amount
                                    </label>

                                    <input type="text" id="received_amount" readonly
                                        class="w-full bg-gray-50 border rounded-lg px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Balance Amount
                                    </label>

                                    <input type="text" id="balance_amount" readonly
                                        class="w-full bg-gray-50 border rounded-lg px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Allottee Name
                                    </label>

                                    <input type="text" id="allottee_name" readonly
                                        class="w-full bg-gray-50 border rounded-lg px-3 py-2">
                                </div>

                            </div>

                        </div>
                    </section>
                    <footer class="flex items-center space-x-3 pt-6 border-t border-gray-100" data-purpose="form-actions">
                        <button id="generateEmiBtn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Make Installment
                        </button>
                        <a href="<?php echo e(url('/mmsay-department-property-emi-calculation')); ?>"
                            class="px-6 py-2 border border-[#007bff] text-[#007bff] hover:bg-blue-50 font-medium text-sm rounded-custom transition-colors duration-200"
                            type="button">
                            Cancel
                        </a>
                    </footer>

                    <div id="emiPreviewSection" class="hidden mt-6">

                        <div class="bg-blue-600 text-white px-4 py-2 rounded-t-lg">
                            EMI Schedule
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-b-lg">
                            <table class="min-w-full text-sm text-center">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-3 py-2">Installment No</th>
                                        <th class="border px-3 py-2">Due Date</th>
                                        <th class="border px-3 py-2">EMI Amount</th>
                                        <th class="border px-3 py-2">Principal</th>
                                        <th class="border px-3 py-2">Balance</th>
                                    </tr>
                                </thead>

                                <tbody id="emiScheduleBody"></tbody>

                            </table>
                        </div>

                    </div>
                    <input type="hidden" id="remaining_balance_hidden">
                    <input type="hidden" id="possession_date_hidden">

                    <!-- END: ActionButtons -->
                </div>
            </main>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {

            // =========================
            // District -> Cities
            // =========================
            $('#district_id').on('change', function() {

                let districtId = $(this).val();

                $('#city_id').html('<option value="">Loading...</option>');
                $('#sector_id').html('<option value="">Select Sector</option>');
                $('#asset_id').html('<option value="">Select Asset</option>');

                $.get("<?php echo e(url('emi-get-cities')); ?>", {
                    district_id: districtId
                }, function(res) {

                    let html = '<option value="">Select City</option>';

                    $.each(res, function(i, row) {
                        html += `<option value="${row.CityId}">
                            ${row.CityName}
                        </option>`;
                    });

                    $('#city_id').html(html);
                });

            });


            // =========================
            // City -> Sector
            // =========================
            $('#city_id').on('change', function() {

                $.get("<?php echo e(url('emi-get-sectors')); ?>", {
                    city_id: $(this).val()
                }, function(res) {

                    let html = '<option value="">Select Sector</option>';

                    $.each(res, function(i, row) {
                        html += `<option value="${row.SectorId}">
                            ${row.SectorName}
                        </option>`;
                    });

                    $('#sector_id').html(html);
                });

            });


            // =========================
            // Sector -> Assets
            // =========================
            $('#sector_id').on('change', function() {

                $.get("<?php echo e(url('emi-get-assets')); ?>", {
                    district_id: $('#district_id').val(),
                    city_id: $('#city_id').val(),
                    sector_id: $('#sector_id').val()
                }, function(res) {

                    let html = '<option value="">Select Asset</option>';

                    $.each(res, function(i, row) {
                        html += `<option value="${row.AssetId}">
                            ${row.AssetName}
                        </option>`;
                    });

                    $('#asset_id').html(html);
                });

            });


            // =========================
            // Asset Details
            // =========================
            $('#asset_id').change(function() {

                $.get(
                    "<?php echo e(url('emi-get-asset-details')); ?>", {
                        asset_id: $(this).val()
                    },
                    function(data) {

                        console.log(data);

                        $('#flat_cost').val(data.FlatCost);
                        $('#received_amount').val(data.ReceivedAmount);
                        $('#balance_amount').val(data.BalanceAmount);
                        $('#allottee_name').val(data.PrivatePurchaserName);

                        $('#remaining_balance_hidden').val(
                            data.BalanceAmount
                        );

                        $('#possession_date_hidden').val(
                            data.OfferOfPossessionDate
                        );
                    }
                );

            });
            // Generate EMI
            // =========================
            $('#generateEmiBtn').on('click', function() {

                let balance = parseFloat(
                    $('#balance_amount').val()
                );

                if (isNaN(balance) || balance <= 0) {

                    alert(
                        'Please select asset and load property details first.'
                    );

                    return;
                }

                let totalEmi = 36;
                let emiAmount = balance / totalEmi;

                let runningBalance = balance;

                let html = '';

                let possessionDate = $('#possession_date_hidden').val();

                if (!possessionDate) {
                    alert('Offer Of Possession Date not found');
                    return;
                }

                let startDate = new Date(possessionDate);

                for (let i = 1; i <= totalEmi; i++) {

                    let dueDate = new Date(startDate);

                    dueDate.setMonth(
                        dueDate.getMonth() + i
                    );

                    runningBalance -= emiAmount;

                    let formattedDate =
                        dueDate.getDate().toString().padStart(2, '0') +
                        '-' +
                        (dueDate.getMonth() + 1)
                        .toString()
                        .padStart(2, '0') +
                        '-' +
                        dueDate.getFullYear();

                    html += `
    <tr>
        <td class="border px-3 py-2">${i}</td>
        <td class="border px-3 py-2">${formattedDate}</td>
        <td class="border px-3 py-2">₹${emiAmount.toFixed(2)}</td>
        <td class="border px-3 py-2">₹${emiAmount.toFixed(2)}</td>
        <td class="border px-3 py-2">₹${Math.max(runningBalance,0).toFixed(2)}</td>
    </tr>`;
                }

                $('#emiScheduleBody').html(html);

                $('#emiPreviewSection')
                    .removeClass('hidden');

                $('html, body').animate({
                    scrollTop: $('#emiPreviewSection')
                        .offset().top - 100
                }, 500);

            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mmsayDepartmentAuth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\housing-project\resources\views/mmsay/departmentPropertyEmiCalculation.blade.php ENDPATH**/ ?>