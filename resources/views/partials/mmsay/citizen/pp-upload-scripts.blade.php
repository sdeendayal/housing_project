<script>
const PP_ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];
const PP_ALLOWED_MIME = ['application/pdf', 'image/jpeg', 'image/png'];
const PP_MAX_FILE_BYTES = 500 * 1024;

function ppSwalFileError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Invalid File',
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc2626',
    });
}

function ppGetFileExtension(name) {
    const parts = (name || '').split('.');
    return parts.length > 1 ? parts.pop().toLowerCase() : '';
}

function ppValidateSingleFile(input) {
    if (!input || !input.files.length) {
        return true;
    }

    const file = input.files[0];
    const ext = ppGetFileExtension(file.name);

    if (!PP_ALLOWED_EXT.includes(ext)) {
        ppSwalFileError('Only PDF, JPG, JPEG, and PNG files are allowed.');
        input.value = '';
        return false;
    }

    if (file.type && !PP_ALLOWED_MIME.includes(file.type)) {
        ppSwalFileError('Only PDF, JPG, JPEG, and PNG files are allowed.');
        input.value = '';
        return false;
    }

    if (file.size <= 0) {
        ppSwalFileError('The selected file appears to be empty.');
        input.value = '';
        return false;
    }

    if (file.size > PP_MAX_FILE_BYTES) {
        ppSwalFileError('Maximum file size is 500 KB.');
        input.value = '';
        return false;
    }

    return true;
}

function ppInitUploadZone(zoneId, inputId, previewId) {
    const zone = document.getElementById(zoneId);
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!zone || !input) return;

    zone.addEventListener('click', () => input.click());
    zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            if (ppValidateSingleFile(input)) {
                ppShowPreview(input, preview);
                zone.classList.remove('pp-upload-error');
            } else if (preview) {
                preview.innerHTML = '';
                zone.classList.add('pp-upload-error');
            }
        }
    });
    input.addEventListener('change', () => {
        if (ppValidateSingleFile(input)) {
            ppShowPreview(input, preview);
            zone.classList.remove('pp-upload-error');
        } else if (preview) {
            preview.innerHTML = '';
            zone.classList.add('pp-upload-error');
        }
    });
}

function ppShowPreview(input, previewEl) {
    if (!previewEl || !input.files.length) return;
    const file = input.files[0];
    previewEl.innerHTML = '<div class="mt-1.5 rounded-lg border border-sky-200 bg-sky-50 px-2 py-1 text-[10px] font-semibold text-sky-800 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check_circle</span>' + file.name + '</div>';
}
</script>
