{{-- Google Translate Element Script (Optimized & Non-blocking) --}}
<script>
(function () {
    if (window.__gtBodyLoaded) return;
    window.__gtBodyLoaded = true;

    // 1. Google Translate Init Callback
    window.googleTranslateElementInit = function () {
        if (window.google && google.translate) {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,hi',
                autoDisplay: false
            }, 'google_translate_element');
        }
        if (typeof window.fastProtectUI === 'function') {
            window.fastProtectUI();
        }
    };

    // 2. Tidy the Google widget once loaded, then STOP immediately
    function tidyWidget() {
        var box = document.getElementById('google_translate_element');
        var sel = box && box.querySelector('select');
        if (sel && !sel.dataset.tidy) {
            sel.dataset.tidy = '1';
            sel.className = 'goog-te-combo form-select form-select-sm';
            
            // Clean unneeded child elements from gadget
            var gadget = box.querySelector('.goog-te-gadget');
            if (gadget) {
                Array.prototype.slice.call(gadget.childNodes).forEach(function (node) {
                    if (node.nodeType === 3 || (node.nodeType === 1 && node !== sel && !node.contains(sel))) {
                        gadget.removeChild(node);
                    }
                });
            }

            var first = sel.options[0];
            if (first) {
                first.text = 'भाषा (Language)';
            }

            // Hook into change event for instant Home/Scheme updates
            sel.addEventListener('change', function () {
                setTimeout(function () {
                    if (typeof window.fastProtectUI === 'function') {
                        window.fastProtectUI();
                    }
                }, 80);
            });

            return true; // Successfully tidied!
        }
        return false;
    }

    // Check periodically only until the select appears, then clear immediately
    var tries = 0;
    var timer = setInterval(function () {
        if (tidyWidget() || ++tries > 20) {
            clearInterval(timer);
        }
    }, 120);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tidyWidget);
    } else {
        tidyWidget();
    }
})();
</script>
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" defer></script>
