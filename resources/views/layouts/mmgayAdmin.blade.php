<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9f9ff;
        }

        /* Custom Scrollbar for administrative density */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    <!-- Shared Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container": "#e7eeff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f0f3ff",
                        "secondary-fixed-dim": "#b7c8e1",
                        "on-primary-fixed": "#001a41",
                        "tertiary": "#595c5e",
                        "surface-dim": "#cfdaf2",
                        "outline": "#717786",
                        "secondary-fixed": "#d3e4fe",
                        "outline-variant": "#c1c6d7",
                        "on-secondary-fixed": "#0b1c30",
                        "inverse-on-surface": "#ecf1ff",
                        "surface-container-high": "#dee8ff",
                        "surface": "#f9f9ff",
                        "on-surface-variant": "#414755",
                        "on-primary": "#ffffff",
                        "tertiary-fixed-dim": "#c4c7c9",
                        "secondary": "#505f76",
                        "on-primary-container": "#fefcff",
                        "surface-variant": "#d8e3fb",
                        "on-secondary-fixed-variant": "#38485d",
                        "background": "#f9f9ff",
                        "surface-container-highest": "#d8e3fb",
                        "on-background": "#111c2d",
                        "on-primary-fixed-variant": "#004493",
                        "surface-tint": "#005bc1",
                        "primary": "#0058bc",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#e0e3e5",
                        "on-tertiary-fixed": "#191c1e",
                        "error": "#ba1a1a",
                        "primary-fixed-dim": "#adc6ff",
                        "inverse-primary": "#adc6ff",
                        "primary-container": "#0070eb",
                        "on-secondary-container": "#54647a",
                        "secondary-container": "#d0e1fb",
                        "on-tertiary-container": "#fbfdff",
                        "on-tertiary-fixed-variant": "#444749",
                        "on-secondary": "#ffffff",
                        "inverse-surface": "#263143",
                        "on-error": "#ffffff",
                        "on-surface": "#111c2d",
                        "tertiary-container": "#727577",
                        "primary-fixed": "#d8e2ff",
                        "surface-bright": "#f9f9ff",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "md": "16px",
                        "xs": "4px",
                        "xl": "32px",
                        "gutter": "20px",
                        "container-max": "1440px",
                        "sm": "8px",
                        "base": "4px",
                        "lg": "24px"
                    },
                    "fontFamily": {
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-lg": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "stat-value": ["Inter"],
                        "headline-md": ["Inter"],
                        "label-md": ["Inter"]
                    },
                    "fontSize": {
                        "body-lg": ["16px", {
                            "lineHeight": "24px",
                            "fontWeight": "400"
                        }],
                        "body-md": ["14px", {
                            "lineHeight": "20px",
                            "fontWeight": "400"
                        }],
                        "headline-lg": ["24px", {
                            "lineHeight": "32px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }],
                        "headline-xl": ["30px", {
                            "lineHeight": "38px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "headline-lg-mobile": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "stat-value": ["28px", {
                            "lineHeight": "34px",
                            "fontWeight": "700"
                        }],
                        "headline-md": ["20px", {
                            "lineHeight": "28px",
                            "fontWeight": "600"
                        }],
                        "label-md": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.05em",
                            "fontWeight": "600"
                        }]
                    }
                },
            },
        }
    </script>
</head>

<body class="flex min-h-screen overflow-y-auto">
    @include('mmgay.super-admin.mmgayadminSidebar')


    @yield('content')

    {{-- Footer --}}
    @include('mmgay.super-admin.footeradminScript')

</body>

</html>
