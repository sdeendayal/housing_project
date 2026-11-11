<style>
    /* ==========================================================================
       Google Translate Top Banner & Layout Shift Elimination (Pure CSS - 0ms CPU)
       ========================================================================== */
    
    /* 1. Eliminate the Google Translate top banner frame completely across ALL pages */
    .goog-te-banner-frame,
    .goog-te-banner-frame.skiptranslate,
    iframe.goog-te-banner-frame,
    iframe[class*="goog-te-banner-frame"],
    iframe[id*="goog-te-banner-frame"],
    .goog-te-balloon-frame,
    #goog-gt-tt,
    .goog-tooltip,
    .goog-tooltip:hover {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        max-height: 0 !important;
        max-width: 0 !important;
        position: absolute !important;
        top: -99999px !important;
        left: -99999px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 none !important;
        pointer-events: none !important;
        z-index: -9999 !important;
    }

    /* 2. Hide any skiptranslate banner containers injected directly into body */
    body > .skiptranslate,
    body > iframe.goog-te-banner-frame,
    body > iframe[src*="javascript:"] {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        position: absolute !important;
        top: -99999px !important;
        pointer-events: none !important;
    }

    /* 3. Keep the actual language switcher dropdown visible and aligned inside its container */
    #google_translate_element,
    #google_translate_element .skiptranslate,
    #google_translate_element .goog-te-gadget {
        display: inline-flex !important;
        align-items: center !important;
        visibility: visible !important;
        position: static !important;
    }

    /* 4. Permanently force html & body to top: 0px and static positioning */
    html {
        top: 0px !important;
    }
    body {
        top: 0px !important;
        position: static !important;
        min-height: 100% !important;
    }

    /* 5. Prevent Google translate text highlight and hover underlines */
    .goog-text-highlight {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    /* 6. Clean, compact, professional styling for the language dropdown widget */
    #google_translate_element select,
    #google_translate_element .goog-te-combo {
        display: inline-block !important;
        padding: 3px 8px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        line-height: 1.4 !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        background-repeat: no-repeat !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        cursor: pointer !important;
        outline: none !important;
        height: 28px !important;
        transition: all 0.15s ease-in-out !important;
        font-family: inherit !important;
    }
    #google_translate_element select:hover,
    #google_translate_element .goog-te-combo:hover {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
    }
    #google_translate_element select:focus,
    #google_translate_element .goog-te-combo:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important;
    }

    /* Hide Google branding & clutter in the widget for a clean UI */
    .goog-te-gadget {
        font-size: 0px !important;
        color: transparent !important;
        line-height: 0 !important;
    }
    .goog-te-gadget .goog-te-combo {
        margin: 0 !important;
    }
    .goog-logo-link,
    .goog-te-gadget-icon {
        display: none !important;
    }
</style>

<script>
    /* Ultra-Fast, Non-Blocking Google Translate Shield */
    (function () {
        if (window.__gtHeadLoaded) return;
        window.__gtHeadLoaded = true;

        // 1. By default language is Hindi across the entire site unless user switched it
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

        var isProtecting = false;
        var HINDI_RESTORE_REGEX = /एमएमएसवाई|एमएमजीए|एमएमजीवाय|एमएमजीएवी|ईडब्ल्यूएस|पीएमएवाई/;
        var HINDI_RESTORE_MAP = [
            { regex: /एमएमएसवाई/g, replace: 'MMSAY' },
            { regex: /एमएमजीए/g, replace: 'MMGAY' },
            { regex: /एमएमजीवाय/g, replace: 'MMGAY' },
            { regex: /एमएमजीएवी/g, replace: 'MMGAV' },
            { regex: /ईडब्ल्यूएस/g, replace: 'EWS' },
            { regex: /पीएमएवाई/g, replace: 'PMAY' }
        ];

        // 2. High-performance targeted protection
        function fastProtectUI() {
            if (isProtecting) return;
            isProtecting = true;

            try {
                // Check if current mode is Hindi
                var isHindi = true;
                var cookies = document.cookie.split(';');
                for (var c = 0; c < cookies.length; c++) {
                    var ck = cookies[c].trim();
                    if (ck.indexOf('googtrans=') === 0) {
                        isHindi = (ck.indexOf('/en/hi') !== -1 || ck.indexOf('/auto/hi') !== -1);
                        break;
                    }
                }

                // 2A. Update 'Home' navigation tab ('होम' in Hindi, 'Home' in English)
                var homeEls = document.querySelectorAll('.nav-home-text');
                for (var h = 0; h < homeEls.length; h++) {
                    var hel = homeEls[h];
                    var htxt = hel.textContent.trim();
                    if (isHindi) {
                        if (htxt === 'घर' || htxt === 'Home') {
                            hel.innerHTML = '<span class="notranslate" translate="no">होम</span>';
                        }
                    } else {
                        if (htxt === 'होम' || htxt === 'घर') {
                            hel.innerHTML = '<span class="notranslate" translate="no">Home</span>';
                        }
                    }
                }

                // 2B. Fast-protect icon elements
                var icons = document.querySelectorAll('.material-symbols-outlined:not(.notranslate), .material-symbols-rounded:not(.notranslate), .material-symbols-sharp:not(.notranslate), .material-icons:not(.notranslate), [class*="material-symbols"]:not(.notranslate), .fa:not(.notranslate), [class*="fa-"]:not(.notranslate), .bi:not(.notranslate), [data-lucide]:not(.notranslate)');
                for (var i = 0; i < icons.length; i++) {
                    icons[i].classList.add('notranslate');
                    icons[i].setAttribute('translate', 'no');
                }

                // 2C. Fast-protect captcha elements
                var captchas = document.querySelectorAll('#captchaText:not(.notranslate), .captcha-badge:not(.notranslate)');
                for (var cap = 0; cap < captchas.length; cap++) {
                    captchas[cap].classList.add('notranslate');
                    captchas[cap].setAttribute('translate', 'no');
                }

                // 2D. Reverse-restore scheme names only if present on page
                if (document.body && HINDI_RESTORE_REGEX.test(document.body.innerText)) {
                    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                    var node;
                    while ((node = walker.nextNode())) {
                        if (HINDI_RESTORE_REGEX.test(node.nodeValue)) {
                            var text = node.nodeValue;
                            for (var m = 0; m < HINDI_RESTORE_MAP.length; m++) {
                                text = text.replace(HINDI_RESTORE_MAP[m].regex, HINDI_RESTORE_MAP[m].replace);
                            }
                            node.nodeValue = text;
                            if (node.parentElement) {
                                node.parentElement.classList.add('notranslate');
                                node.parentElement.setAttribute('translate', 'no');
                            }
                        }
                    }
                }
            } catch (err) {
                // Silently avoid performance degradation
            } finally {
                isProtecting = false;
            }
        }

        window.fastProtectUI = fastProtectUI;
        window.protectAllUIElements = fastProtectUI;

        // 3. Debounced MutationObserver (Max once every 120ms, zero main-thread freezing)
        var debounceTimer = null;
        function requestProtection() {
            if (debounceTimer) return;
            debounceTimer = setTimeout(function () {
                debounceTimer = null;
                fastProtectUI();
            }, 120);
        }

        if (window.MutationObserver) {
            var observer = new MutationObserver(function (mutations) {
                for (var m = 0; m < mutations.length; m++) {
                    var mut = mutations[m];
                    if (mut.type === 'characterData' || (mut.type === 'childList' && mut.addedNodes.length > 0)) {
                        requestProtection();
                        break;
                    }
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                fastProtectUI();
                if (document.body) {
                    observer.observe(document.body, {
                        childList: true,
                        subtree: true,
                        characterData: true
                    });
                }
            });
        } else {
            document.addEventListener('DOMContentLoaded', fastProtectUI);
        }
    })();
</script>
