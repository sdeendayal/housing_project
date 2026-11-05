{{-- Google Translate Element Script --}}
<script>
(function () {
    if (window.__gtBodyLoaded) return;
    window.__gtBodyLoaded = true;

    // 1. Ensure default language is Hindi if no cookie was set
    var hasGoogtrans = document.cookie.split(';').some(function (item) {
        return item.trim().indexOf('googtrans=') === 0;
    });
    if (!hasGoogtrans) {
        document.cookie = 'googtrans=/en/hi; path=/';
        var host = window.location.hostname;
        if (host && !/^[0-9.]+$/.test(host)) {
            document.cookie = 'googtrans=/en/hi; path=/; domain=.' + host;
        }
    }

    // 2. Trigger protection
    function triggerUIProtection() {
        if (typeof window.protectAllUIElements === 'function') {
            window.protectAllUIElements(document);
        }
        if (typeof window.suppressGoogleBanner === 'function') {
            window.suppressGoogleBanner();
        }
    }
    window.triggerUIProtection = triggerUIProtection;
    triggerUIProtection();

    // 3. Define the global callback that Google's element.js calls
    window.googleTranslateElementInit = function () {
        triggerUIProtection();

        if (window.google && google.translate) {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,hi',
                autoDisplay: false
            }, 'google_translate_element');
        }
    };

    // 4. Tidy the Google widget: keep language dropdown, hide top banner, format options
    function tidyWidget() {
        var tries = 0;
        var timer = setInterval(function () {
            triggerUIProtection();

            var box = document.getElementById('google_translate_element');
            var sel = box && box.querySelector('select');
            if (sel && !sel.dataset.tidy) {
                sel.dataset.tidy = '1';
                sel.className = 'goog-te-combo form-select form-select-sm';
                sel.addEventListener('change', function () {
                    setTimeout(function () {
                        triggerUIProtection();
                    }, 50);
                    setTimeout(function () {
                        triggerUIProtection();
                    }, 300);
                });
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
            }
            var banners = document.querySelectorAll('.goog-te-banner-frame, iframe.goog-te-banner-frame, body > .skiptranslate');
            for (var b = 0; b < banners.length; b++) {
                banners[b].style.setProperty('display', 'none', 'important');
                banners[b].style.setProperty('visibility', 'hidden', 'important');
                banners[b].style.setProperty('height', '0px', 'important');
            }
            if (document.body) {
                if (document.body.style.top && document.body.style.top !== '0px') {
                    document.body.style.setProperty('top', '0px', 'important');
                }
                if (document.body.style.position === 'relative') {
                    document.body.style.setProperty('position', 'static', 'important');
                }
            }
            if (++tries > 50) {
                clearInterval(timer);
            }
        }, 250);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', tidyWidget);
    } else {
        tidyWidget();
    }
})();
</script>
<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
