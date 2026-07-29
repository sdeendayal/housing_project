<!-- Global SweetAlert2 Toast Notification Partial -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    function showGlobalToast() {
        if (typeof Swal === 'undefined') return;

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-xl shadow-2xl border border-slate-200 font-sans'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        <?php if(session('success')): ?>
            <?php
                $msg = session('success');
                $isLogout = (str_contains(strtolower($msg), 'logout') || str_contains(strtolower($msg), 'logged out'));
            ?>
            Toast.fire({
                icon: "<?php echo e($isLogout ? 'info' : 'success'); ?>",
                title: <?php echo json_encode($msg, 15, 512) ?>
            });
        <?php elseif(session('error')): ?>
            Toast.fire({
                icon: 'error',
                title: <?php echo json_encode(session('error'), 15, 512) ?>
            });
        <?php elseif(session('warning')): ?>
            Toast.fire({
                icon: 'warning',
                title: <?php echo json_encode(session('warning'), 15, 512) ?>
            });
        <?php elseif(session('info')): ?>
            Toast.fire({
                icon: 'info',
                title: <?php echo json_encode(session('info'), 15, 512) ?>
            });
        <?php endif; ?>
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showGlobalToast);
    } else {
        showGlobalToast();
    }
})();
</script>
<?php /**PATH E:\sports\housing_project\resources\views/partials/global-toast.blade.php ENDPATH**/ ?>