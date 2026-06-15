<script>
(function () {
    const form = document.getElementById('ppApplyForm');
    if (!form) return;

    const MAX_BYTES = 10 * 1024 * 1024;
    const ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];
    const ALLOWED_MIME = ['application/pdf', 'image/jpeg', 'image/png'];

    const uploadFields = [
@foreach(\App\Models\PhysicalPossessionDocument::applyFormFields() as $field => $meta)
@if(!in_array($field, [\App\Models\PhysicalPossessionDocument::TYPE_POSSESSION_CERTIFICATE, \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER], true))
        { inputId: 'input_{{ $field }}', zoneId: 'zone_{{ $field }}', label: @json($meta['label']), required: true },
@endif
@endforeach
    ];

    const manualAllotmentInput = document.getElementById('input_{{ \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER }}');
    if (manualAllotmentInput && manualAllotmentInput.getAttribute('data-required') === '1') {
        uploadFields.push({
            inputId: 'input_{{ \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER }}',
            zoneId: 'zone_{{ \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER }}',
            label: 'Allotment Letter',
            required: true
        });
    }

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
        uploadFields.forEach(function (field) {
            document.getElementById(field.zoneId)?.classList.remove('pp-upload-error');
        });
        document.getElementById('possessionCertSection')?.classList.remove('ring-2', 'ring-red-300');
        document.getElementById('allotmentLetterSection')?.classList.remove('ring-2', 'ring-red-300');
    }

    function highlightField(field) {
        clearFieldHighlight();
        if (field.zoneId === 'possessionCertSection' || field.zoneId === 'allotmentLetterSection') {
            document.getElementById(field.zoneId)?.classList.add('ring-2', 'ring-red-300');
            document.getElementById(field.zoneId)?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
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

    function isAllotmentComplete() {
        const allotmentFlag = document.getElementById('allotmentLetterVerified');
        if (allotmentFlag && allotmentFlag.value === '1') {
            return true;
        }

        const allotmentInput = document.getElementById('input_{{ \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER }}');
        if (allotmentInput && allotmentInput.getAttribute('data-required') === '1') {
            return allotmentInput.files && allotmentInput.files.length > 0;
        }

        return false;
    }

    function validateFormFirst() {
        const possessionFlag = document.getElementById('possessionCertificateVerified');
        if (!possessionFlag || possessionFlag.value !== '1') {
            return {
                error: 'Please verify your Possession Certificate (Document 1) before submitting.',
                field: { zoneId: 'possessionCertSection' }
            };
        }

        if (!isAllotmentComplete()) {
            const allotmentInput = document.getElementById('input_{{ \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER }}');
            if (allotmentInput && allotmentInput.getAttribute('data-required') === '1') {
                const fileError = validateFileFirst(allotmentInput, 'Allotment Letter', true);
                if (fileError) {
                    return { error: fileError, field: { zoneId: 'zone_{{ \App\Models\PhysicalPossessionDocument::TYPE_ALLOTMENT_LETTER }}' } };
                }
            }

            return {
                error: 'Please verify your Allotment Letter (Document 2) before submitting.',
                field: { zoneId: 'allotmentLetterSection' }
            };
        }

        for (let i = 0; i < uploadFields.length; i++) {
            const field = uploadFields[i];
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
                title: 'All 5 Documents Required',
                text: result.error,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc2626',
            });
            return;
        }

        citizenSwalConfirm({
            title: 'Submit Application?',
            text: 'All 5 documents are complete. Please confirm they are correct. You cannot edit after submission.',
            confirmButtonText: 'Yes, Submit',
        }).then(function (confirmResult) {
            if (confirmResult.isConfirmed) {
                form.submit();
            }
        });
    });
})();
</script>
