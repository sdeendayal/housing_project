<script>
(function () {
    const form = document.getElementById('ppApplyForm');
    if (!form) return;

    const MAX_BYTES = 10 * 1024 * 1024;
    const ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];
    const ALLOWED_MIME = ['application/pdf', 'image/jpeg', 'image/png'];

    const fields = [
        { inputId: 'input_filled_form', zoneId: 'zone_filled_form', label: 'Signed Possession Certificate Request Form' },
        { inputId: 'input_registration_certificate', zoneId: 'zone_registration_certificate', label: 'Registration Certificate' },
        { inputId: 'input_provisional_possession_letter', zoneId: 'zone_provisional_possession_letter', label: 'Provisional Possession Letter' },
    ];

    function getExtension(name) {
        const parts = (name || '').split('.');
        return parts.length > 1 ? parts.pop().toLowerCase() : '';
    }

    function formatSize(bytes) {
        if (bytes < 1024 * 1024) {
            return Math.round(bytes / 1024) + ' KB';
        }
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function clearFieldHighlight() {
        fields.forEach(function (field) {
            document.getElementById(field.zoneId)?.classList.remove('pp-upload-error');
        });
    }

    function highlightField(field) {
        clearFieldHighlight();
        document.getElementById(field.zoneId)?.classList.add('pp-upload-error');
        document.getElementById(field.zoneId)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /** Returns first error message for one field, or null if valid */
    function validateFileFirst(input, label) {
        if (!input || !input.files || !input.files.length) {
            return label + ' is required.';
        }

        const file = input.files[0];
        const ext = getExtension(file.name);

        if (!ALLOWED_EXT.includes(ext)) {
            return label + ': only PDF, JPG, JPEG, PNG files are allowed.';
        }

        if (file.type && !ALLOWED_MIME.includes(file.type)) {
            return label + ': invalid file type.';
        }

        if (file.size <= 0) {
            return label + ': file appears to be empty.';
        }

        if (file.size > MAX_BYTES) {
            return label + ': file size is ' + formatSize(file.size) + '. Maximum allowed is 10 MB.';
        }

        return null;
    }

    /** Check fields one by one — stop at first error */
    function validateFormFirst() {
        for (let i = 0; i < fields.length; i++) {
            const field = fields[i];
            const input = document.getElementById(field.inputId);
            const error = validateFileFirst(input, field.label);

            if (error) {
                return { error: error, field: field };
            }
        }

        return null;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        clearFieldHighlight();

        const result = validateFormFirst();
        if (result) {
            highlightField(result.field);
            Swal.fire({
                icon: 'error',
                title: 'Validation Required',
                text: result.error,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
            });
            return;
        }

        citizenSwalConfirm({
            title: 'Submit Application?',
            text: 'Please confirm all uploaded documents are correct. You cannot edit after submission.',
            confirmButtonText: 'Yes, Submit',
        }).then(function (confirmResult) {
            if (confirmResult.isConfirmed) {
                form.submit();
            }
        });
    });
})();
</script>
