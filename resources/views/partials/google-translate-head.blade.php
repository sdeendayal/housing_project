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

    /* 7. Fast bilingual visibility for marked elements without JS wait */
    html.lang-hi .lang-show-en {
        display: none !important;
    }
    html.lang-hi .lang-show-hi {
        display: inline !important;
    }
    html.lang-en .lang-show-en {
        display: inline !important;
    }
    html.lang-en .lang-show-hi {
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

        // Helper to check language and toggle html.lang-hi / html.lang-en immediately
        function updateLangClass() {
            var isHindi = true;
            var cookies = document.cookie.split(';');
            for (var c = 0; c < cookies.length; c++) {
                var ck = cookies[c].trim();
                if (ck.indexOf('googtrans=') === 0) {
                    isHindi = (ck.indexOf('/en/hi') !== -1 || ck.indexOf('/auto/hi') !== -1);
                    break;
                }
            }
            if (isHindi) {
                document.documentElement.classList.add('lang-hi');
                document.documentElement.classList.remove('lang-en');
            } else {
                document.documentElement.classList.add('lang-en');
                document.documentElement.classList.remove('lang-hi');
            }
            return isHindi;
        }

        // Apply class immediately in <head> before <body> renders
        updateLangClass();

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

        // Replacement maps for Department of Housing For All & Housing For All
        var HINDI_DEPT_REPLACEMENTS = [
            // Google Translate literal translations to official Hindi
            { regex: /सभी के लिए आवास विभाग,\s*हरियाणा सरकार/g, replace: 'हाउसिंग फॉर ऑल विभाग, हरियाणा सरकार' },
            { regex: /सभी के लिए आवास विभाग,\s*हरियाणा/g, replace: 'हाउसिंग फॉर ऑल विभाग, हरियाणा' },
            { regex: /सभी के लिए आवास विभाग/g, replace: 'हाउसिंग फॉर ऑल विभाग' },
            { regex: /सबके लिए आवास विभाग/g, replace: 'हाउसिंग फॉर ऑल विभाग' },
            { regex: /सभी के लिए आवास,\s*हरियाणा/g, replace: 'हाउसिंग फॉर ऑल, हरियाणा' },
            { regex: /सभी के लिए आवास/g, replace: 'हाउसिंग फॉर ऑल' },
            { regex: /सबके लिए आवास/g, replace: 'हाउसिंग फॉर ऑल' },

            // English phrases to official Hindi
            { regex: /Department of Housing [Ff]or All,\s*Government of Haryana/g, replace: 'हाउसिंग फॉर ऑल विभाग, हरियाणा सरकार' },
            { regex: /Department of Housing [Ff]or All\s*•\s*Government of Haryana/g, replace: 'हाउसिंग फॉर ऑल विभाग • हरियाणा सरकार' },
            { regex: /Department of Housing [Ff]or All,\s*Haryana/g, replace: 'हाउसिंग फॉर ऑल विभाग, हरियाणा' },
            { regex: /Department of Housing [Ff]or All/g, replace: 'हाउसिंग फॉर ऑल विभाग' },
            { regex: /DEPARTMENT OF HOUSING FOR ALL,\s*HARYANA/g, replace: 'हाउसिंग फॉर ऑल विभाग, हरियाणा' },
            { regex: /DEPARTMENT OF HOUSING FOR ALL/g, replace: 'हाउसिंग फॉर ऑल विभाग' },
            { regex: /Housing [Ff]or All Department,\s*Haryana/g, replace: 'हाउसिंग फॉर ऑल विभाग, हरियाणा' },
            { regex: /Housing [Ff]or All Department/g, replace: 'हाउसिंग फॉर ऑल विभाग' },
            { regex: /Directorate of Housing [Ff]or All/g, replace: 'हाउसिंग फॉर ऑल निदेशालय' },
            { regex: /Housing [Ff]or All,\s*Haryana/g, replace: 'हाउसिंग फॉर ऑल, हरियाणा' },
            { regex: /Housing [Ff]or All Haryana/g, replace: 'हाउसिंग फॉर ऑल, हरियाणा' },
            { regex: /Housing [Ff]or All/g, replace: 'हाउसिंग फॉर ऑल' },
            { regex: /HOUSING FOR ALL/g, replace: 'हाउसिंग फॉर ऑल' },

            // Location name corrections (Charkhi Dadri & Jagadhri)
            { regex: /चरकी[\s\-]*दादरी/g, replace: 'चरखी दादरी' },
            { regex: /जगधरी/g, replace: 'जगाधरी' },
            { regex: /Charki[\s\-]*Dadri/gi, replace: 'चरखी दादरी' },
            { regex: /Charkhi[\s\-]*Dadri/gi, replace: 'चरखी दादरी' },
            { regex: /Jagadhri/gi, replace: 'जगाधरी' },

            // Vision & Help corrections
            { regex: /हमारा\s+नज़रिया|हमारा\s+नजरिया/g, replace: 'हमारा लक्ष्य' },
            { regex: /(^|[^\u0900-\u097F])मदद([^\u0900-\u097F]|$)/g, replace: '$1सहायता$2' }
        ];

        var ENGLISH_DEPT_REPLACEMENTS = [
            { regex: /हाउसिंग फॉर ऑल विभाग,\s*हरियाणा सरकार/g, replace: 'Department of Housing For All, Government of Haryana' },
            { regex: /हाउसिंग फॉर ऑल विभाग\s*•\s*हरियाणा सरकार/g, replace: 'Department of Housing for All • Government of Haryana' },
            { regex: /हाउसिंग फॉर ऑल विभाग,\s*हरियाणा/g, replace: 'Department of Housing For All, Haryana' },
            { regex: /हाउसिंग फॉर ऑल विभाग/g, replace: 'Department of Housing For All' },
            { regex: /हाउसिंग फॉर ऑल निदेशालय/g, replace: 'Directorate of Housing For All' },
            { regex: /हाउसिंग फॉर ऑल,\s*हरियाणा/g, replace: 'Housing For All, Haryana' },
            { regex: /हाउसिंग फॉर ऑल/g, replace: 'Housing For All' },
            { regex: /चरखी दादरी/g, replace: 'Charkhi Dadri' },
            { regex: /जगाधरी/g, replace: 'Jagadhri' },
            { regex: /हमारा लक्ष्य/g, replace: 'Our Vision' },
            { regex: /सहायता/g, replace: 'Help' }
        ];

        // 2. High-performance targeted protection
        function fastProtectUI() {
            if (isProtecting) return;
            isProtecting = true;

            try {
                // Check if current mode is Hindi and update root class
                var isHindi = updateLangClass();

                // 2A. Update 'Home', 'Our Vision', and 'Help' navigation links
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

                // Protect and update any Our Vision / Help navigation links without bilingual wrapper
                var visionLinks = document.querySelectorAll('a[href*="vision"]:not(.notranslate)');
                for (var vl = 0; vl < visionLinks.length; vl++) {
                    var vnode = visionLinks[vl];
                    var vtxt = vnode.textContent.trim();
                    if (isHindi) {
                        if (vtxt === 'Our Vision' || vtxt === 'हमारा नज़रिया' || vtxt === 'हमारा नजरिया') {
                            vnode.innerHTML = '<span class="material-symbols-outlined notranslate text-[18px]" translate="no">visibility</span> <span class="notranslate" translate="no">हमारा लक्ष्य</span>';
                            vnode.classList.add('notranslate');
                            vnode.setAttribute('translate', 'no');
                        }
                    }
                }

                var helpLinks = document.querySelectorAll('a[href*="help"]:not(.notranslate)');
                for (var hl = 0; hl < helpLinks.length; hl++) {
                    var hnode = helpLinks[hl];
                    var htxt = hnode.textContent.trim();
                    if (isHindi) {
                        if (htxt === 'Help' || htxt === 'मदद' || htxt === 'HELP') {
                            hnode.innerHTML = '<span class="material-symbols-outlined notranslate text-[18px]" translate="no">help</span> <span class="notranslate" translate="no">सहायता</span>';
                            hnode.classList.add('notranslate');
                            hnode.setAttribute('translate', 'no');
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

                // 2D. Update document title
                if (document.title) {
                    if (isHindi) {
                        if (/Department of Housing [Ff]or All|Housing [Ff]or All/i.test(document.title)) {
                            document.title = document.title
                                .replace(/Department of Housing [Ff]or All/gi, 'हाउसिंग फॉर ऑल विभाग')
                                .replace(/Housing [Ff]or All/gi, 'हाउसिंग फॉर ऑल');
                        }
                    } else {
                        if (/हाउसिंग फॉर ऑल विभाग|हाउसिंग फॉर ऑल/.test(document.title)) {
                            document.title = document.title
                                .replace(/हाउसिंग फॉर ऑल विभाग/g, 'Department of Housing For All')
                                .replace(/हाउसिंग फॉर ऑल/g, 'Housing For All');
                        }
                    }
                }

                // 2E. Sync local page selectors if present
                var localSelectors = ['aboutLanguage', 'whoLanguage', 'orgLanguage', 'visionLanguage'];
                for (var s = 0; s < localSelectors.length; s++) {
                    var selEl = document.getElementById(localSelectors[s]);
                    if (selEl) {
                        var targetVal = isHindi ? 'hi' : 'en';
                        if (selEl.value !== targetVal) {
                            selEl.value = targetVal;
                            selEl.dispatchEvent(new Event('change'));
                        }
                    }
                }

                // 2F. Process text nodes across body for Department of Housing For All, Locations, Nav & Schemes
                var targetDeptRegex = isHindi
                    ? /Department of Housing|Housing [Ff]or All|HOUSING FOR ALL|सभी के लिए आवास|सबके लिए आवास|चरकी|जगधरी|Charkhi|Charki|Jagadhri|हमारा\s+नज़रिया|हमारा\s+नजरिया|मदद/i
                    : /हाउसिंग फॉर ऑल|चरखी दादरी|जगाधरी|हमारा लक्ष्य|सहायता/;

                var bodyText = document.body ? (document.body.innerText || '') : '';
                var shouldProcessDept = targetDeptRegex.test(bodyText);
                var shouldProcessSchemes = isHindi && HINDI_RESTORE_REGEX.test(bodyText);

                if (document.body && (shouldProcessDept || shouldProcessSchemes)) {
                    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                    var node;
                    while ((node = walker.nextNode())) {
                        var parent = node.parentElement;
                        if (!parent) continue;

                        var tag = parent.tagName;
                        if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'TEXTAREA' || tag === 'INPUT' || tag === 'CODE' || tag === 'PRE') {
                            continue;
                        }

                        // Skip elements inside bilingual containers with specific data-attributes if in English mode
                        if (!isHindi && parent.closest && parent.closest('[data-about-language="hi"], [data-org-lang="hi"], [data-vision-lang="hi"], [data-vision-content="hi"], [data-who-lang="hi"]')) {
                            continue;
                        }

                        // Skip elements that already have dedicated bilingual classes
                        if (parent.closest && parent.closest('.lang-show-en, .lang-show-hi')) {
                            continue;
                        }

                        var text = node.nodeValue;
                        var originalText = text;

                        // Restore English scheme acronyms (MMSAY, etc.) if translated
                        if (isHindi && HINDI_RESTORE_REGEX.test(text)) {
                            for (var m = 0; m < HINDI_RESTORE_MAP.length; m++) {
                                text = text.replace(HINDI_RESTORE_MAP[m].regex, HINDI_RESTORE_MAP[m].replace);
                            }
                        }

                        // Department of Housing For All & Housing For All translations
                        if (isHindi) {
                            if (targetDeptRegex.test(text)) {
                                for (var d = 0; d < HINDI_DEPT_REPLACEMENTS.length; d++) {
                                    text = text.replace(HINDI_DEPT_REPLACEMENTS[d].regex, HINDI_DEPT_REPLACEMENTS[d].replace);
                                }
                            }
                        } else {
                            if (targetDeptRegex.test(text)) {
                                for (var e = 0; e < ENGLISH_DEPT_REPLACEMENTS.length; e++) {
                                    text = text.replace(ENGLISH_DEPT_REPLACEMENTS[e].regex, ENGLISH_DEPT_REPLACEMENTS[e].replace);
                                }
                            }
                        }

                        if (text !== originalText) {
                            node.nodeValue = text;
                            if (isHindi) {
                                parent.classList.add('notranslate');
                                parent.setAttribute('translate', 'no');
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
