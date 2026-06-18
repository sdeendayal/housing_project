<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    window.citizenSwal = function (options) {
        return Swal.fire(Object.assign({
            confirmButtonText: 'OK',
            confirmButtonColor: '#4f46e5',
            showConfirmButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: undefined,
            timerProgressBar: false,
        }, options || {}));
    };

    window.citizenSwalConfirm = function (options) {
        return Swal.fire(Object.assign({
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            allowOutsideClick: false,
            allowEscapeKey: false,
        }, options || {}));
    };

    window.citizenSwalWarning = function (title, text) {
        return citizenSwal({ icon: 'warning', title: title, text: text });
    };

    window.citizenSwalError = function (title, text) {
        return citizenSwal({ icon: 'error', title: title, text: text });
    };

    @if (session('success') && ! request()->routeIs('citizen.login', 'citizen.login.verify-page'))
        citizenSwal({ icon: 'success', title: 'Success', text: @json(session('success')) });
    @endif

    @if (session('warning'))
        citizenSwal({ icon: 'warning', title: @json(session('warning_title', 'Notice')), text: @json(session('warning')) });
    @endif

    @if (session('error'))
        citizenSwalError('Error', @json(session('error')));
    @endif

    @if (isset($errors) && $errors->any())
        citizenSwalError('Validation Error', @json($errors->first()));
    @endif
})();
</script>
