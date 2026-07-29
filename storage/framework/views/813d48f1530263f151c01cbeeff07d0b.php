<div id="citizen-toast-root" class="fixed top-20 right-4 z-[9999] flex flex-col gap-2 pointer-events-none max-w-sm w-full sm:w-80"></div>

<style>
    .citizen-toast {
        pointer-events: auto;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        color: #fff;
        font-size: 14px;
        line-height: 1.4;
        animation: citizenToastIn 0.35s ease;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .citizen-toast-success {
        background: linear-gradient(135deg, #1b6d24, #2e7d32);
    }

    .citizen-toast-error {
        background: linear-gradient(135deg, #ba1a1a, #c62828);
    }

    .citizen-toast-info {
        background: linear-gradient(135deg, #003358, #0B5CAD);
    }

    .citizen-toast-icon {
        font-size: 22px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .citizen-toast-hide {
        animation: citizenToastOut 0.3s ease forwards;
    }

    @keyframes citizenToastIn {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes citizenToastOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
</style>

<script>
    (function () {
        const root = document.getElementById('citizen-toast-root');
        if (!root) return;

        const icons = {
            success: 'check_circle',
            error: 'error',
            info: 'info',
        };

        window.showCitizenToast = function (message, type = 'success', duration = 4500) {
            if (!message) return;

            const toast = document.createElement('div');
            toast.className = 'citizen-toast citizen-toast-' + type;
            toast.innerHTML =
                '<span class="material-symbols-outlined citizen-toast-icon">' + (icons[type] || icons.info) +
                '</span><span class="flex-1 font-medium">' + message + '</span>';

            root.appendChild(toast);

            setTimeout(function () {
                toast.classList.add('citizen-toast-hide');
                setTimeout(function () {
                    toast.remove();
                }, 300);
            }, duration);
        };

        
    })();
</script>
<?php /**PATH E:\sports\housing_project\resources\views/partials/mmsay/citizen-toast.blade.php ENDPATH**/ ?>