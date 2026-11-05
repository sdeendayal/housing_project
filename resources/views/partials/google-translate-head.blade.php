<style>
    /* ==========================================================================
       Google Translate Top Banner & Layout Shift Elimination
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
    /* Google Translate Layout Protection & UI Preservation */
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

        // 2. Suppress Google Translate Top Banner and lock body position
        function suppressGoogleBanner() {
            if (document.body) {
                if (document.body.style.top && document.body.style.top !== '0px') {
                    document.body.style.setProperty('top', '0px', 'important');
                }
                if (document.body.style.position && document.body.style.position === 'relative') {
                    document.body.style.setProperty('position', 'static', 'important');
                }
            }
            if (document.documentElement && document.documentElement.style.top && document.documentElement.style.top !== '0px') {
                document.documentElement.style.setProperty('top', '0px', 'important');
            }

            var bannerFrames = document.querySelectorAll('.goog-te-banner-frame, iframe.goog-te-banner-frame, body > .skiptranslate');
            for (var i = 0; i < bannerFrames.length; i++) {
                var el = bannerFrames[i];
                if (el.style.display !== 'none') {
                    el.style.setProperty('display', 'none', 'important');
                    el.style.setProperty('visibility', 'hidden', 'important');
                    el.style.setProperty('height', '0px', 'important');
                }
            }
        }

        var isProtecting = false;

        // 3. Scheme acronyms & brand names that must NEVER be translated into Hindi
        var SCHEME_REGEX = /\b(MMSAY|MMGAY|MMGAV|EWS|PMAY(?:[-–]?[UG]|\([UG]\))?|PPP|CRID|HFA)\b/i;
        var BRAND_REGEX = /housing\s+for\s+all/i;

        // Reverse map: if Google Translate or cache translated these to Hindi phonetics, immediately restore them to English
        var HINDI_RESTORE_MAP = [
            { regex: /एमएमएसवाई/g, replace: 'MMSAY' },
            { regex: /एमएमजीए/g, replace: 'MMGAY' },
            { regex: /एमएमजीवाय/g, replace: 'MMGAY' },
            { regex: /एमएमजीएवी/g, replace: 'MMGAV' },
            { regex: /ईडब्ल्यूएस/g, replace: 'EWS' },
            { regex: /पीएमएवाई/g, replace: 'PMAY' }
        ];

        function protectSchemeAndBrandNames(root) {
            if (isProtecting) return;
            var scope = root || document.body;
            if (!scope) return;

            isProtecting = true;
            try {
                // Step A: Reverse-restore any Hindi translated scheme abbreviations back to English
                var walkerA = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT, null, false);
                var nodeA;
                var hindiNodes = [];
                while ((nodeA = walkerA.nextNode())) {
                    if (/एमएमएसवाई|एमएमजीए|एमएमजीवाय|एमएमजीएवी|ईडब्ल्यूएस|पीएमएवाई/.test(nodeA.nodeValue)) {
                        hindiNodes.push(nodeA);
                    }
                }
                for (var h = 0; h < hindiNodes.length; h++) {
                    var hn = hindiNodes[h];
                    var text = hn.nodeValue;
                    for (var m = 0; m < HINDI_RESTORE_MAP.length; m++) {
                        text = text.replace(HINDI_RESTORE_MAP[m].regex, HINDI_RESTORE_MAP[m].replace);
                    }
                    hn.nodeValue = text;
                    if (hn.parentElement) {
                        hn.parentElement.classList.add('notranslate');
                        hn.parentElement.setAttribute('translate', 'no');
                    }
                }

                // Step B: Protect English acronyms and brand names
                var walkerB = document.createTreeWalker(scope, NodeFilter.SHOW_TEXT, null, false);
                var nodeB;
                var nodesToProcess = [];
                while ((nodeB = walkerB.nextNode())) {
                    var parent = nodeB.parentElement;
                    if (!parent) continue;
                    var tag = parent.tagName;
                    if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'NOSCRIPT' || tag === 'TEXTAREA' || tag === 'TITLE') continue;
                    if (parent.closest && parent.closest('.notranslate, [translate="no"]')) continue;

                    var textVal = nodeB.nodeValue;
                    if (SCHEME_REGEX.test(textVal) || BRAND_REGEX.test(textVal)) {
                        nodesToProcess.push(nodeB);
                    }
                }

                for (var i = 0; i < nodesToProcess.length; i++) {
                    var tNode = nodesToProcess[i];
                    var pNode = tNode.parentNode;
                    if (!pNode) continue;

                    var trimmed = tNode.nodeValue.trim();
                    // If the entire text node is just the acronym / brand, protect parent directly
                    if (/^(MMSAY|MMGAY|MMGAV|EWS|PMAY(?:[-–]?[UG]|\([UG]\))?|PPP|CRID|HFA|Housing\s+For\s+All)$/i.test(trimmed)) {
                        if (pNode.tagName !== 'BODY' && pNode.tagName !== 'HTML') {
                            pNode.classList.add('notranslate');
                            pNode.setAttribute('translate', 'no');
                            continue;
                        }
                    }

                    // If mixed text, split into text and protected spans
                    var combinedSplit = /(\b(?:MMSAY|MMGAY|MMGAV|EWS|PMAY(?:[-–]?[UG]|\([UG]\))?|PPP|CRID|HFA)\b|housing\s+for\s+all)/i;
                    var testMatch = /(\b(?:MMSAY|MMGAY|MMGAV|EWS|PMAY(?:[-–]?[UG]|\([UG]\))?|PPP|CRID|HFA)\b|housing\s+for\s+all)/i;
                    var parts = tNode.nodeValue.split(combinedSplit);
                    if (parts.length > 1) {
                        var frag = document.createDocumentFragment();
                        for (var p = 0; p < parts.length; p++) {
                            if (testMatch.test(parts[p])) {
                                var span = document.createElement('span');
                                span.className = 'notranslate';
                                span.setAttribute('translate', 'no');
                                span.textContent = parts[p];
                                frag.appendChild(span);
                            } else if (parts[p]) {
                                frag.appendChild(document.createTextNode(parts[p]));
                            }
                        }
                        pNode.replaceChild(frag, tNode);
                    }
                }
            } catch (e) {
                console.warn('Translate protection error:', e);
            } finally {
                isProtecting = false;
            }
        }

        // 4. Protect all icon fonts, badges, captchas & ligatures
        function protectIconsAndUI(root) {
            var scope = root || document;
            if (!scope.querySelectorAll) return;

            // Icons (Material Symbols, FontAwesome, Bootstrap Icons, Lucide)
            var icons = scope.querySelectorAll(
                '.material-symbols-outlined, .material-symbols-rounded, .material-symbols-sharp, .material-icons, [class*="material-symbols"], [class*="material-icon"], .fa, [class*="fa-"], .notranslate-icon, .bi, [class*="bi-"], [data-lucide], i[class*="bi"]'
            );
            for (var i = 0; i < icons.length; i++) {
                var el = icons[i];
                if (!el.classList.contains('notranslate')) {
                    el.classList.add('notranslate');
                }
                if (el.getAttribute('translate') !== 'no') {
                    el.setAttribute('translate', 'no');
                }
            }

            // Captcha Codes
            var captchas = scope.querySelectorAll('#captchaText, .captcha-badge, [id*="captchaText"], [class*="captcha-badge"]');
            for (var c = 0; c < captchas.length; c++) {
                var cap = captchas[c];
                if (!cap.classList.contains('notranslate')) {
                    cap.classList.add('notranslate');
                }
                if (cap.getAttribute('translate') !== 'no') {
                    cap.setAttribute('translate', 'no');
                }
            }

            // Captcha Labels
            var labels = scope.querySelectorAll('label, .input-label');
            for (var l = 0; l < labels.length; l++) {
                var lbl = labels[l];
                if (/captcha/i.test(lbl.textContent) && !lbl.classList.contains('notranslate')) {
                    lbl.classList.add('notranslate');
                    lbl.setAttribute('translate', 'no');
                }
            }

            // Badges & Acronyms
            var badges = scope.querySelectorAll('.badge-scheme, .portal-group-label, .pp-scheme-new-badge-sm');
            for (var b = 0; b < badges.length; b++) {
                var badge = badges[b];
                if (!badge.classList.contains('notranslate')) {
                    badge.classList.add('notranslate');
                }
                if (badge.getAttribute('translate') !== 'no') {
                    badge.setAttribute('translate', 'no');
                }
            }
        }

        // 5. Ensure 'Home' navigation tab is rendered as 'होम' in Hindi instead of 'घर'
        function updateHomeNavText() {
            var isHindi = false;
            var cookies = document.cookie.split(';');
            var hasCookie = false;
            for (var i = 0; i < cookies.length; i++) {
                var c = cookies[i].trim();
                if (c.indexOf('googtrans=') === 0) {
                    hasCookie = true;
                    if (c.indexOf('/en/hi') !== -1 || c.indexOf('/auto/hi') !== -1) {
                        isHindi = true;
                    }
                    break;
                }
            }
            if (!hasCookie) {
                isHindi = true;
            }

            var homeLabels = document.querySelectorAll('.nav-home-text');
            for (var k = 0; k < homeLabels.length; k++) {
                var el = homeLabels[k];
                var current = el.textContent.trim();
                if (isHindi) {
                    if (current === 'घर' || current === 'Home') {
                        el.innerHTML = '<span class="notranslate" translate="no">होम</span>';
                    }
                } else {
                    if (current === 'होम' || current === 'घर') {
                        el.innerHTML = '<span class="notranslate" translate="no">Home</span>';
                    }
                }
            }

            // Also check any navigation anchor with text 'घर' that was translated from 'Home'
            var navLinks = document.querySelectorAll('nav a, header a, .navbar a');
            for (var n = 0; n < navLinks.length; n++) {
                var link = navLinks[n];
                for (var cn = 0; cn < link.childNodes.length; cn++) {
                    var child = link.childNodes[cn];
                    if (child.nodeType === 3 && child.nodeValue.trim() === 'घर') {
                        if (isHindi) {
                            var span = document.createElement('span');
                            span.className = 'notranslate';
                            span.setAttribute('translate', 'no');
                            span.textContent = 'होम';
                            link.replaceChild(span, child);
                        }
                    }
                }
            }
        }

        function protectAll(root) {
            protectIconsAndUI(root);
            protectSchemeAndBrandNames(root);
            updateHomeNavText();
            suppressGoogleBanner();
        }

        window.protectAllUIElements = protectAll;
        window.suppressGoogleBanner = suppressGoogleBanner;

        // MutationObserver intercepts any node as it is parsed or added dynamically
        if (window.MutationObserver) {
            new MutationObserver(function (mutations) {
                suppressGoogleBanner();
                for (var i = 0; i < mutations.length; i++) {
                    var m = mutations[i];
                    if (m.type === 'childList') {
                        for (var j = 0; j < m.addedNodes.length; j++) {
                            var node = m.addedNodes[j];
                            if (node.nodeType === 1) {
                                if (node.classList && (node.classList.contains('goog-te-banner-frame') || (node.classList.contains('skiptranslate') && node.parentElement === document.body))) {
                                    node.style.setProperty('display', 'none', 'important');
                                    node.style.setProperty('height', '0px', 'important');
                                }
                                if (node.matches && node.matches('.material-symbols-outlined, .material-symbols-rounded, .material-symbols-sharp, .material-icons, [class*="material-symbols"], .bi, [class*="bi-"], [data-lucide]')) {
                                    node.classList.add('notranslate');
                                    node.setAttribute('translate', 'no');
                                }
                                protectAll(node);
                            } else if (node.nodeType === 3) {
                                protectSchemeAndBrandNames(node.parentElement || document.body);
                            }
                        }
                    } else if (m.type === 'characterData') {
                        protectSchemeAndBrandNames(m.target.parentElement || document.body);
                    } else if (m.type === 'attributes' && m.target === document.body) {
                        suppressGoogleBanner();
                    }
                }
            }).observe(document.documentElement, {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }

        // Periodic check to ensure Google's asynchronous scripts never re-show banner or push body
        var bannerCheckCount = 0;
        var bannerInterval = setInterval(function () {
            suppressGoogleBanner();
            protectSchemeAndBrandNames(document.body);
            bannerCheckCount++;
            if (bannerCheckCount > 60) {
                clearInterval(bannerInterval);
            }
        }, 200);

        document.addEventListener('DOMContentLoaded', function () {
            protectAll(document);
            suppressGoogleBanner();
        });

        // Run immediately
        protectAll(document);
        suppressGoogleBanner();
    })();
</script>