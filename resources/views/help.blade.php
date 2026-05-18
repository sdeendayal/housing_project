<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Help - Housing For All</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-tertiary-fixed-variant": "#00522f",
                        "secondary-container": "#7db6ff",
                        "surface-dim": "#d7dadc",
                        "tertiary-fixed": "#91f8b8",
                        "surface": "#f7fafc",
                        "error": "#ba1a1a",
                        "on-secondary": "#ffffff",
                        "tertiary-container": "#003f23",
                        "outline-variant": "#c4c6cf",
                        "on-background": "#181c1e",
                        "tertiary-fixed-dim": "#74db9d",
                        "surface-tint": "#455f88",
                        "on-surface": "#181c1e",
                        "on-primary": "#ffffff",
                        "primary-fixed-dim": "#adc7f7",
                        "surface-container-low": "#f1f4f6",
                        "inverse-on-surface": "#eef1f3",
                        "surface-container-high": "#e5e9eb",
                        "primary-container": "#1a365d",
                        "on-secondary-fixed-variant": "#004881",
                        "inverse-primary": "#adc7f7",
                        "secondary-fixed": "#d3e4ff",
                        "outline": "#74777f",
                        "background": "#f7fafc",
                        "secondary": "#1960a3",
                        "surface-container-highest": "#e0e3e5",
                        "on-secondary-container": "#00477f",
                        "surface-bright": "#f7fafc",
                        "on-tertiary": "#ffffff",
                        "on-surface-variant": "#43474e",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-fixed": "#001b3c",
                        "on-primary-container": "#86a0cd",
                        "on-tertiary-fixed": "#002110",
                        "primary-fixed": "#d6e3ff",
                        "on-secondary-fixed": "#001c38",
                        "surface-container": "#ebeef0",
                        "primary": "#002045",
                        "surface-variant": "#e0e3e5",
                        "secondary-fixed-dim": "#a2c9ff",
                        "inverse-surface": "#2d3133",
                        "on-primary-fixed-variant": "#2d476f",
                        "on-tertiary-container": "#4bb278",
                        "error-container": "#ffdad6",
                        "tertiary": "#002713",
                        "on-error": "#ffffff",
                        "on-error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xs": "4px",
                        "md": "24px",
                        "gutter": "24px",
                        "container-max": "1280px",
                        "sm": "12px",
                        "base": "8px",
                        "xl": "80px",
                        "lg": "48px"
                    },
                    "fontFamily": {
                        "headline-sm": ["Inter"],
                        "body-sm": ["Inter"],
                        "display-lg-mobile": ["Inter"],
                        "headline-lg": ["Inter"],
                        "headline-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "display-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-xl": ["Inter"]
                    },
                    "fontSize": {
                        "headline-sm": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "body-sm": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "display-lg-mobile": ["32px", {
                            "lineHeight": "40px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "700"
                        }],
                        "headline-lg": ["30px", {
                            "lineHeight": "38px",
                            "fontWeight": "600"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "32px",
                            "fontWeight": "600"
                        }],
                        "label-sm": ["12px", {
                            "lineHeight": "14px",
                            "fontWeight": "600"
                        }],
                        "display-lg": ["48px", {
                            "lineHeight": "56px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "body-lg": ["18px", {
                            "lineHeight": "28px",
                            "fontWeight": "400"
                        }],
                        "body-md": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "label-md": ["14px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.05em",
                            "fontWeight": "500"
                        }],
                        "headline-xl": ["36px", {
                            "lineHeight": "44px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }]
                    }
                },
            },
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 1);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .contact-item-shadow {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .notch-left {
            position: relative;
        }

        .notch-left::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
            border-right: 12px solid currentColor;
        }
    </style>
</head>

<body class="bg-surface text-on-surface font-body-md overflow-x-hidden">
    <!-- TopNavBar -->
    <header
        class="text-on-primary docked full-width top-0 sticky border-b border-outline-variant shadow-md z-50 bg-[linear-gradient(90deg,rgba(6,127,208,1)_0%,rgba(0,51,88,1)_100%)] bg-[url('../header-tp-bg.png')] bg-no-repeat bg-right bg-cover">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Placeholder for Department Logo -->
                <img alt="Department Logo" class="h-16 w-16 object-contain" src="Haryana_emblem.png" />
                <div>
                    <h1 class="text-2xl font-bold leading-tight">Department of Housing For All</h1>
                    <p class="text-sm opacity-90">Government of Haryana</p>
                </div>
            </div>
            <!-- National Emblem Placeholder -->
            <img alt="National Emblem" class="h-16 w-auto object-contain hidden md:block" src="emblem-black.png" />
        </div>
    </header>

    <nav class="bg-white border-b border-slate-200 shadow-sm relative z-40">
        <div class="max-w-7xl mx-auto px-4">
            <div
                class="flex flex-wrap items-center justify-center md:justify-start space-x-1 md:space-x-6 py-2 text-center">
                <a class="px-3 py-2 text-sm font-medium text-civic-blue border-b-2 border-civic-accent" href="/">
                    Home</a>
                <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">About Us</a>
                <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">Our Vision</a>
                <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors"
                    href="#">Gallery</a>
                <!-- Highlighted Button -->
                <a class="px-4 py-2 text-sm font-bold text-civic-blue bg-civic-highlight hover:bg-yellow-500 rounded-md shadow-sm transition-colors uppercase tracking-wide"
                    href="/help">
                    Help
                </a>
                <!-- <a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors" href="#">Contact Us</a>
<a class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-civic-blue hover:bg-slate-50 rounded-md transition-colors" href="#">Suggestion Box</a>
<div class="relative ml-auto flex-1 md:flex-none mt-2 md:mt-0 w-full md:w-auto">
<input class="w-full md:w-48 pl-3 pr-8 py-1.5 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-1 focus:ring-civic-accent focus:border-civic-accent" placeholder="Search..." type="text"/>
<svg class="w-4 h-4 text-slate-400 absolute right-2.5 top-2" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg> -->
            </div>
        </div>
        </div>
    </nav>


    <main class="pt-xl pb-xl" style="    padding-top: 0px;">
        <!-- Hero / Help Header -->
        {{-- <section class="max-w-container-max mx-auto px-gutter mt-20 text-center mb-lg">
            <h1 class="font-display-lg text-display-lg text-primary mb-xs">Support Center</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Providing citizen-centric
                assistance for all your housing application needs and queries.</p>
        </section> --}}
        <!-- Main Contact Section (Reference IMAGE_7) -->
        <section class="max-w-container-max mx-auto px-gutter mb-xl">
            <div
                class="flex flex-col lg:flex-row items-center justify-center gap-xl bg-surface-container-low rounded-xl p-lg shadow-sm">
                <!-- Illustration Side -->
                <div class="w-full lg:w-1/2 flex justify-center">
                    <img alt="Contact Support Illustration" class="w-full max-w-[400px] h-auto object-contain"
                        data-alt="A clean, professional 3D-style illustration of a large yellow envelope with a white document inside featuring a prominent grey at-symbol. The graphic is set against a soft, high-key light mode background with subtle ambient shadows, mirroring a modern government portal's minimalist and reliable aesthetic. The lighting is soft and directional, emphasizing the rounded corners and friendly corporate design."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAdXd0sxXDBRqyOJwqW6_JIwreJygZxV6m9jIuLHahMrbYhHJfF0712srklWqbsxqRRuogTjkAse1mGb7Ry-mMR5SwjkF9TVAwUNQKW-BSy2zfq4Es5g8-F0Udj5hfSc1eIvIGzyN5YkABqWpr26U_Ye3Cdj_00PHpWOiSQFCcVlHRbBZUnySTUxDdS3vVJlY25H3nFpfkrH8RjgvMo2aLGq3yzxv_5SL9r-rH6P2qXLEpeK2GwoOSMbYPBwinb1wab5X03dyucHVY" />
                </div>
                <!-- Contact Card Side -->
                <div class="w-full lg:w-1/2">
                    <div
                        class="glass-card p-lg rounded-xl shadow-[0px_12px_40px_rgba(26,54,93,0.12)] border border-white max-w-[540px] mx-auto">
                        <h2 class="font-headline-lg text-headline-lg text-on-surface text-center mb-lg">We are here to
                            help you</h2>
                        <div class="space-y-md">
                            <!-- Address -->
                            <div class="flex items-center gap-md">
                                <div
                                    class="bg-[#005f63] text-on-primary font-body-md text-body-md py-sm px-lg rounded-lg notch-left flex-1 contact-item-shadow text-center">
                                    C-15, Awas Bhawan, Sector 6, Panchkula, Haryana
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="flex items-center gap-md">
                                <div
                                    class="bg-[#bda621] text-on-primary font-body-md text-body-md py-sm px-lg rounded-lg notch-left flex-1 contact-item-shadow text-center">
                                    admin-hfa[at]hry[dot]gov[dot]in
                                </div>
                            </div>
                            <!-- Phone Numbers -->
                            <div class="flex items-center gap-md">
                                <div
                                    class="bg-[#ba3000] text-on-primary font-body-md text-body-md py-sm px-lg rounded-lg notch-left flex-1 contact-item-shadow text-center">
                                    0172-2585852
                                </div>
                            </div>
                            <div class="flex items-center gap-md">
                                <div
                                    class="bg-[#ba3000] text-on-primary font-body-md text-body-md py-sm px-lg rounded-lg notch-left flex-1 contact-item-shadow text-center">
                                    0172-2568687
                                </div>
                            </div>
                            <div class="flex items-center gap-md">
                                <div
                                    class="bg-[#ba3000] text-on-primary font-body-md text-body-md py-sm px-lg rounded-lg notch-left flex-1 contact-item-shadow text-center">
                                    0172-2567233
                                </div>
                            </div>
                        </div>
                        <div class="mt-lg text-center">
                            <a class="inline-block px-xl py-sm bg-primary text-on-primary font-label-md rounded-lg hover:bg-primary-container transition-all hover:translate-y-[-2px] active:scale-95 shadow-md"
                                href="#">
                                Go to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- FAQ Section -->
        <section class="max-w-[900px] mx-auto px-gutter mb-xl">
            <div class="text-center mb-lg">
                <h2 class="font-headline-xl text-headline-xl text-primary mb-xs">Frequently Asked Questions</h2>
                <div class="h-1 w-20 bg-secondary-container mx-auto rounded-full"></div>
            </div>
            <div class="space-y-sm">
                <!-- FAQ Item 1 -->
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
                    <button
                        class="w-full flex justify-between items-center p-md text-left hover:bg-surface-container-low transition-colors">
                        <span class="font-headline-sm text-headline-sm text-on-surface">How to apply for MMSAY?</span>
                        <span class="material-symbols-outlined text-primary">expand_more</span>
                    </button>
                    <div class="px-md pb-md text-on-surface-variant font-body-md">
                        Citizens can apply for the Mukhyamantri Shahari Awas Yojana (MMSAY) through the official portal
                        by registering with their Parivar Pehchan Patra (PPP) ID and completing the online application
                        form with required demographic and income details.
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
                    <button
                        class="w-full flex justify-between items-center p-md text-left hover:bg-surface-container-low transition-colors">
                        <span class="font-headline-sm text-headline-sm text-on-surface">Document requirements for EWS
                            category?</span>
                        <span class="material-symbols-outlined text-primary">expand_more</span>
                    </button>
                    <div class="px-md pb-md text-on-surface-variant font-body-md">
                        Essential documents include the Parivar Pehchan Patra, Income Certificate showing annual income
                        below ₹1.80 Lakh, Domicile of Haryana, and Aadhaar Card. Self-declaration forms may also be
                        required for specific verification processes.
                    </div>
                </div>
                <!-- FAQ Item 3 -->
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
                    <button
                        class="w-full flex justify-between items-center p-md text-left hover:bg-surface-container-low transition-colors">
                        <span class="font-headline-sm text-headline-sm text-on-surface">How can I check my application
                            status?</span>
                        <span class="material-symbols-outlined text-primary">expand_more</span>
                    </button>
                    <div class="px-md pb-md text-on-surface-variant font-body-md">
                        Login to the dashboard using your registered credentials. Navigate to the 'My Applications'
                        section where you can track the real-time status of your submission, from verification to
                        approval.
                    </div>
                </div>
                <!-- FAQ Item 4 -->
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden">
                    <button
                        class="w-full flex justify-between items-center p-md text-left hover:bg-surface-container-low transition-colors">
                        <span class="font-headline-sm text-headline-sm text-on-surface">Is there a processing fee for
                            the application?</span>
                        <span class="material-symbols-outlined text-primary">expand_more</span>
                    </button>
                    <div class="px-md pb-md text-on-surface-variant font-body-md">
                        Generally, registration for the housing scheme is free of cost for EWS applicants. However,
                        check the latest notification on the 'Our Vision' page for any nominal administrative charges
                        that might apply to different housing categories.
                    </div>
                </div>
            </div>
        </section>
        <!-- Grid Cards for Help Categories -->
        <section class="max-w-container-max mx-auto px-gutter mb-xl grid grid-cols-1 md:grid-cols-3 gap-md">
            <div
                class="bg-white p-lg rounded-xl shadow-sm border border-outline-variant flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div
                    class="w-16 h-16 bg-primary-fixed rounded-full flex items-center justify-center mb-md text-primary">
                    <span class="material-symbols-outlined text-[32px]">description</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm mb-xs">User Manuals</h3>
                <p class="text-on-surface-variant font-body-sm mb-md">Step-by-step guides for navigating the portal and
                    filling out applications.</p>
                <a class="text-secondary font-label-md hover:underline" href="#">Download PDF</a>
            </div>
            <div
                class="bg-white p-lg rounded-xl shadow-sm border border-outline-variant flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div
                    class="w-16 h-16 bg-tertiary-fixed rounded-full flex items-center justify-center mb-md text-on-tertiary-fixed-variant">
                    <span class="material-symbols-outlined text-[32px]">video_library</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm mb-xs">Video Tutorials</h3>
                <p class="text-on-surface-variant font-body-sm mb-md">Watch video walk-throughs to understand the
                    registration process easily.</p>
                <a class="text-secondary font-label-md hover:underline" href="#">Watch on YouTube</a>
            </div>
            <div
                class="bg-white p-lg rounded-xl shadow-sm border border-outline-variant flex flex-col items-center text-center hover:shadow-md transition-shadow">
                <div
                    class="w-16 h-16 bg-secondary-fixed rounded-full flex items-center justify-center mb-md text-on-secondary-fixed-variant">
                    <span class="material-symbols-outlined text-[32px]">location_on</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm mb-xs">Service Centers</h3>
                <p class="text-on-surface-variant font-body-sm mb-md">Find the nearest Antyodaya Saral Kendra for
                    offline assistance.</p>
                <a class="text-secondary font-label-md hover:underline" href="#">View Map</a>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <footer class="bg-slate-800 text-slate-300 py-8 text-sm">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>© 2026 Department of Housing For All, Government of Haryana, India.</p>
            <p class="mt-2 text-slate-500">Designed & Developed by Citizen Resources Information Department, Haryana
                (CRID)</p>
        </div>
    </footer>
</body>

</html>
