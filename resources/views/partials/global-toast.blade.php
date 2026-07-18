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

        @if (session('success'))
            @php
                $msg = session('success');
                $isLogout = (str_contains(strtolower($msg), 'logout') || str_contains(strtolower($msg), 'logged out'));
            @endphp
            Toast.fire({
                icon: "{{ $isLogout ? 'info' : 'success' }}",
                title: @json($msg)
            });
        @elseif (session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error'))
            });
        @elseif (session('warning'))
            Toast.fire({
                icon: 'warning',
                title: @json(session('warning'))
            });
        @elseif (session('info'))
            Toast.fire({
                icon: 'info',
                title: @json(session('info'))
            });
        @endif
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showGlobalToast);
    } else {
        showGlobalToast();
    }
})();
</script>
