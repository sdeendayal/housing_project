<!-- Global Form Submission Loader -->
<script>
    (function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Find the submit button inside the form
            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            
            // Safeguard: If the submit button is already disabled (custom script handled it), skip global loader
            if (submitBtn && submitBtn.disabled) {
                return;
            }
            
            // Allow skipping loader using data attribute
            if (form.getAttribute('data-no-loader') === 'true') {
                return;
            }
            
            // Trigger native HTML5 validation report
            if (!form.checkValidity()) {
                // If form has errors, native HTML5 validation bubbles show, loader will not display
                return;
            }
            
            // Disable button and show spinner inside it
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                if (submitBtn.tagName === 'BUTTON') {
                    submitBtn.innerHTML = `
                        <svg class="animate-spin h-4 w-4 text-white inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    `;
                }
            }
            
            // Show SweetAlert2 loader if the library is loaded on the page
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Please wait...',
                    text: 'Submitting request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    })();
</script>
