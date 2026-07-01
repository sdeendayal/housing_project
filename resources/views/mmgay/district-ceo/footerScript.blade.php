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
    $(function() {

        loadPhase(1);

        $('.phase-tab').click(function() {

            $('.phase-tab')
                .removeClass('text-primary font-bold border-b-2 border-primary')
                .addClass('text-on-surface-variant');

            $(this)
                .removeClass('text-on-surface-variant')
                .addClass('text-primary font-bold border-b-2 border-primary');

            loadPhase($(this).data('phase'));

        });

    });
</script>
<script>
    function loadPhase(phase) {

        $.ajax({
            url: "{{ route('district.dashboard') }}/" + phase,
            type: "GET",
            dataType: "json",

            success: function(res) {

                console.log(res);

                $('#total').text(res.Total ?? 0);
                $('#paid').text(res.Paid ?? 0);
                $('#approved').text(res.Approved ?? 0);
                $('#rejected').text(res.Rejected ?? 0);
                $('#inprocess').text(res.InProcess ?? 0);
                $('#pending').text(res.Pending ?? 0);
            },

            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });

    }
</script>

<script>
    let currentPhase = 1;

    $('.phase-tab').click(function() {
        currentPhase = $(this).data('phase');
    });

    function openList(status) {
        window.location.href = "/district-ceo/list/" + currentPhase + "/" + status;
    }

    
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
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: '{{ session('success') }}',
    confirmButtonColor: '#2563eb'
});
</script>
@endif

