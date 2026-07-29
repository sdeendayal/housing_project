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

document.querySelectorAll('form[data-pp-loading]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!e.defaultPrevented) {
            document.getElementById('ppLoading')?.classList.add('show');
        }
    });
});

<?php if(session('success')): ?>
    ppSwal({ icon: 'success', title: 'Success', text: <?php echo json_encode(session('success'), 15, 512) ?> });
<?php endif; ?>
<?php if(session('warning')): ?>
    ppSwal({ icon: 'warning', title: 'Please Wait', text: <?php echo json_encode(session('warning'), 15, 512) ?> });
<?php endif; ?>
<?php if(session('error')): ?>
    ppSwal({ icon: 'error', title: 'Error', text: <?php echo json_encode(session('error'), 15, 512) ?> });
<?php endif; ?>

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
<?php /**PATH D:\xampp\htdocs\housing-project\resources\views/physical-possession/partials/scripts.blade.php ENDPATH**/ ?>