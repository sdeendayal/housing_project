<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MMGAY Deputy Commissioner Dashboard</title>
    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <!-- Lucide Icons (via CDN for consistency with dashboard look) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style data-purpose="custom-styles">
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f6f9;
        }

        .sidebar-gradient {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }

        .hero-gradient {
            background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('mmgay.dc.partials.sidebar')
    <main class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        @include('mmgay.dc.partials.navbar')



        @yield('content')


        @stack('scripts')
        <script data-purpose="canvas-charts">
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Lucide icons
                lucide.createIcons();

                

                // Sparklines
                

                drawMainChart();
                drawDonut();
                drawSparkline('sparkline-1', '#3b82f6');
                drawSparkline('sparkline-2', '#fbbf24');
                drawSparkline('sparkline-3', '#10b981');
                drawSparkline('sparkline-4', '#f43f5e');
                drawSparkline('sparkline-5', '#6366f1');
                drawSparkline('sparkline-6', '#fb923c');
            });
        </script>
        @include('partials.global-loader')
</body>
</html>
