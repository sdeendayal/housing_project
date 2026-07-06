<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link rel="icon" type="image/png" href="favicon.png">
    <title>@yield('title', 'Department of Housing For All')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap"
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
                        "on-secondary": "#ffffff",
                        "tertiary": "#002713",
                        "primary-container": "#1a365d",
                        "secondary-fixed-dim": "#a2c9ff",
                        "secondary-container": "#7db6ff",
                        "surface-container-highest": "#e0e3e5",
                        "inverse-primary": "#adc7f7",
                        "surface-variant": "#e0e3e5",
                        "on-secondary-container": "#00477f",
                        "secondary": "#1960a3",
                        "on-surface": "#181c1e",
                        "background": "#f7fafc",
                        "surface-container-low": "#f1f4f6",
                        "error-container": "#ffdad6",
                        "primary-fixed": "#d6e3ff",
                        "tertiary-fixed-dim": "#74db9d",
                        "on-primary-fixed": "#001b3c",
                        "surface-container": "#ebeef0",
                        "tertiary-container": "#003f23",
                        "outline": "#74777f",
                        "on-tertiary-container": "#4bb278",
                        "surface-dim": "#d7dadc",
                        "tertiary-fixed": "#91f8b8",
                        "on-tertiary-fixed": "#002110",
                        "on-background": "#181c1e",
                        "primary-fixed-dim": "#adc7f7",
                        "on-error-container": "#93000a",
                        "inverse-surface": "#2d3133",
                        "on-tertiary-fixed-variant": "#00522f",
                        "on-secondary-fixed": "#001c38",
                        "on-tertiary": "#ffffff",
                        "surface": "#f7fafc",
                        "primary": "#002045",
                        "on-surface-variant": "#43474e",
                        "on-secondary-fixed-variant": "#004881",
                        "on-primary-container": "#86a0cd",
                        "error": "#ba1a1a",
                        "surface-tint": "#455f88",
                        "on-primary-fixed-variant": "#2d476f",
                        "on-primary": "#ffffff",
                        "surface-container-high": "#e5e9eb",
                        "secondary-fixed": "#d3e4ff",
                        "inverse-on-surface": "#eef1f3",
                        "surface-bright": "#f7fafc",
                        "outline-variant": "#c4c6cf",
                        "on-error": "#ffffff",
                        "surface-container-lowest": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xs": "4px",
                        "sm": "12px",
                        "container-max": "1280px",
                        "xl": "80px",
                        "base": "8px",
                        "md": "24px",
                        "lg": "48px",
                        "gutter": "24px"
                    },
                    "fontFamily": {
                        "headline-sm": ["Inter"],
                        "headline-lg": ["Inter"],
                        "headline-xl": ["Inter"],
                        "display-lg-mobile": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "display-lg": ["Inter"]
                    },
                    "fontSize": {
                        "headline-sm": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "headline-lg": ["30px", {
                            "lineHeight": "38px",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["36px", {
                            "lineHeight": "44px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "display-lg-mobile": ["32px", {
                            "lineHeight": "40px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "700"
                        }],
                        "headline-md": ["24px", {
                            "lineHeight": "32px",
                            "fontWeight": "600"
                        }],
                        "body-sm": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
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
                        "label-sm": ["12px", {
                            "lineHeight": "14px",
                            "fontWeight": "600"
                        }],
                        "display-lg": ["48px", {
                            "lineHeight": "56px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }]
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0px 4px 20px rgba(26, 54, 93, 0.05);
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-background text-on-background min-h-screen">
    {{-- Header --}}
    @include('partials.mmsay.department.sideNavBar')
    @include('partials.mmsay.department.topHeader')

    {{-- Page Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.mmsay.department.departmentScripts')

    @include('partials.global-loader')
</body>
</html>
