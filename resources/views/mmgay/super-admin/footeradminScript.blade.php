<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Micro-interactions Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Simple logic for branch selector toggle simulation
        const branchBtn = document.querySelector('button[class*="Branch Selector"]'); // Placeholder selector
        // Since we built the HTML manually based on guidelines, let's use the actual button
        const realBranchBtn = document.querySelector('header .relative button');

        if (realBranchBtn) {
            realBranchBtn.addEventListener('click', () => {
                console.log('Branch selector toggled');
            });
        }

        // Tab logic
        const tabButtons = document.querySelectorAll('.mb-lg button');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                tabButtons.forEach(b => {
                    b.classList.remove('text-primary', 'font-bold', 'border-b-2',
                        'border-primary');
                    b.classList.add('text-on-surface-variant');
                });
                btn.classList.add('text-primary', 'font-bold', 'border-b-2', 'border-primary');
                btn.classList.remove('text-on-surface-variant');
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        $('.phase-tab').click(function() {

            let phase = $(this).data('phase');

            $('.phase-tab')
                .removeClass('bg-blue-600 text-white')
                .addClass('bg-white');

            $(this)
                .removeClass('bg-white')
                .addClass('bg-blue-600 text-white');

            $.ajax({

                url: "/district-ceo/dashboard/" + phase,

                type: "GET",

                dataType: "json",

                success: function(res) {

                    // =========================
                    // Cards
                    // =========================

                    $('#totalVillages').text(res.totals.totalVillages);
                    $('#totalPlots').text(res.totals.totalPlots);
                    $('#totalApplicants').text(res.totals.totalApplicants);
                    $('#totalPaid').text(res.totals.totalPaid);
                    $('#totalAllotment').text(res.totals.totalAllotment);
                    $('#totalPossession').text(res.totals.totalPossession);

                    // Phase Text
                    $('#phaseTitle').text("Phase " + res.phase + " Village Statistics");

                    // =========================
                    // Table
                    // =========================

                    let tbody = "";

                    let gtPlots = 0;
                    let gtApplicants = 0;
                    let gtPaid = 0;
                    let gtAllotment = 0;
                    let gtSC = 0;
                    let gtGhumantu = 0;
                    let gtWidow = 0;
                    let gtOthers = 0;

                    $.each(res.villageData, function(index, row) {

                        tbody += `
<tr class="border-b hover:bg-blue-50">

    <td class="px-4 py-3">${index + 1}</td>

    <td class="px-4 py-3 font-medium">${row.VillageName}</td>

    <td class="px-4 py-3 text-center">${row.TotalPlots}</td>

    <td class="px-4 py-3 text-center">${row.TotalApplicants}</td>

    <td class="px-4 py-3 text-center text-green-600 font-semibold">
        ${row.Paid}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.SC}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.Ghumantu}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.Widow}
    </td>

    <td class="px-4 py-3 text-center">
        ${row.Others}
    </td>

    <td class="px-4 py-3 text-center text-blue-600 font-semibold">
        ${row.TotalAllotment}
    </td>

</tr>`;
                    });

                    $('#villageTableBody').html(tbody);

                    // Grand Total - Controller se
                    $('#gtPlots').text(res.totals.totalPlots);
                    $('#gtApplicants').text(res.totals.totalApplicants);
                    $('#gtPaid').text(res.totals.totalPaid);

                    $('#gtSC').text(res.totals.totalSC);
                    $('#gtGhumantu').text(res.totals.totalGhumantu);
                    $('#gtWidow').text(res.totals.totalWidow);
                    $('#gtOthers').text(res.totals.totalOthers);

                    $('#gtAllotment').text(res.totals.totalAllotment);

                }

            });

        });

    });
</script>
<script>
    document.getElementById('grievanceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Submit Grievance?',
            text: "Once submitted, the application will move to Pending status.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#2563eb'
        });
    </script>
@endif
