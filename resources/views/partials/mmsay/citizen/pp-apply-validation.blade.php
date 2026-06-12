<script>
(function () {
    const form = document.getElementById('ppApplyForm');
    if (!form) return;

    const MAX_BYTES = 10 * 1024 * 1024;
    const ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];
    const ALLOWED_MIME = ['application/pdf', 'image/jpeg', 'image/png'];

    const fields = [
@foreach(\App\Models\PhysicalPossessionDocument::applyFormFields() as $field => $meta)
        { inputId: 'input_{{ $field }}', zoneId: 'zone_{{ $field }}', label: @json($meta['label']), required: {{ $meta['required'] ? 'true' : 'false' }} },
@endforeach
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

    function validateFileFirst(input, label, required) {
        if (!input || !input.files || !input.files.length) {
            if (required) {
                return label + ' is required.';
            }
            return null;
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

    function validateFormFirst() {
        for (let i = 0; i < fields.length; i++) {
            const field = fields[i];
            const input = document.getElementById(field.inputId);
            const error = validateFileFirst(input, field.label, field.required);

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
