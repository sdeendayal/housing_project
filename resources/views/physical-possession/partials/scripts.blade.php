<div id="ppLoading" class="pp-loading">
    <div class="spinner-border text-light spinner-border-sm" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script>
// SweetAlert - sirf OK button se band hoga
function ppSwal(options) {
    return Swal.fire(Object.assign({
        confirmButtonText: 'OK',
        confirmButtonColor: '#1e40af',
        showConfirmButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        timer: undefined,
        timerProgressBar: false,
    }, options || {}));
}

function ppToggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-bs-theme') || 'light';
    const next = current === 'light' ? 'dark' : 'light';
    html.setAttribute('data-bs-theme', next);
    localStorage.setItem('pp-theme', next);
}

(function() {
    const saved = localStorage.getItem('pp-theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', saved);
})();

// Listen to all form submissions to disable buttons and show spinner
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        // Prevent spinner if form validation fails (for standard HTML5 validation)
        if (form.checkValidity && !form.checkValidity()) {
            return;
        }

        // Find submit button(s) in this form
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"], .btn-submit');
        submitButtons.forEach(function(button) {
            // Avoid duplicate spinner if already loading/disabled
            if (button.hasAttribute('disabled') || button.classList.contains('disabled')) {
                return;
            }

            // Disable the button to prevent duplicate submits (double clicks)
            button.setAttribute('disabled', 'true');
            button.classList.add('disabled');

            // Add spinner indicator
            if (button.tagName.toLowerCase() === 'button') {
                const originalHtml = button.innerHTML;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> ' + originalHtml;
            } else if (button.tagName.toLowerCase() === 'input') {
                button.value = 'Please wait...';
            }
        });

        // Also show general overlay loading if configured
        if (form.hasAttribute('data-pp-loading') && !e.defaultPrevented) {
            document.getElementById('ppLoading')?.classList.add('show');
        }
    });
});

@if(session('success'))
    ppSwal({ icon: 'success', title: 'Success', text: @json(session('success')) });
@endif
@if(session('warning'))
    ppSwal({ icon: 'warning', title: 'Please Wait', text: @json(session('warning')) });
@endif
@if(session('error'))
    ppSwal({ icon: 'error', title: 'Error', text: @json(session('error')) });
@endif

document.querySelectorAll('.pp-counter').forEach(function(el) {
    const target = parseInt(el.getAttribute('data-target') || el.textContent);
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 40));
    const timer = setInterval(function() {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = current.toLocaleString('en-IN');
    }, 30);
});

function ppToggleSidebar() {
    document.getElementById('ppSidebar')?.classList.toggle('show');
    document.getElementById('ppSidebarOverlay')?.classList.toggle('show');
}

document.querySelectorAll('.pp-sidebar-link, .pp-sidebar-logout').forEach(function(link) {
    link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
            document.getElementById('ppSidebar')?.classList.remove('show');
            document.getElementById('ppSidebarOverlay')?.classList.remove('show');
        }
    });
});

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
            ppShowPreview(input, preview);
        }
    });
    input.addEventListener('change', () => ppShowPreview(input, preview));
}

function ppShowPreview(input, previewEl) {
    if (!previewEl || !input.files.length) return;
    const file = input.files[0];
    previewEl.innerHTML = '<div class="alert alert-info py-1 px-2 mb-0 small"><i class="bi bi-file-earmark-check me-1"></i>' + file.name + '</div>';
}
</script>
