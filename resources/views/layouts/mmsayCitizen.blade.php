<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $pageTitle ?? 'Citizen Portal' }} - Haryana Housing For All</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @include('partials.mmsay.citizen.styles')
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    @include('partials.mmsay.citizen.sidebar')

    <div class="flex-1 flex flex-col md:ml-[228px] min-h-screen">

        <header class="header-v2 sticky top-0 z-50">
            <div class="flex justify-between items-center px-2.5 sm:px-4 h-[46px] max-w-[1280px] mx-auto w-full">
                <div class="flex items-center gap-2 min-w-0">
                    <button id="menuToggle" type="button" class="md:hidden p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 shrink-0">
                        <span class="material-symbols-outlined text-[20px]">menu</span>
                    </button>
                    <div class="min-w-0">
                        <p class="header-sub text-[9px] font-bold text-indigo-500 uppercase tracking-widest">Citizen Portal</p>
                        <h1 class="text-sm font-extrabold text-slate-800 truncate">{{ $pageTitle ?? 'Dashboard' }}</h1>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('citizen.profile') }}" class="btn-v2-primary btn-v2-sm">
                        <span class="material-symbols-outlined text-[14px]">account_circle</span>
                        <span class="hidden sm:inline">Profile</span>
                    </a>
                    <a href="{{ route('citizen.logout') }}" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50" title="Logout">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="main-v2 flex-1 px-2 py-2 sm:px-3 md:px-4 max-w-[1280px] mx-auto w-full pb-3 space-y-2">
            @yield('content')
        </main>

        <footer class="footer-v2 py-2 px-2.5 sm:px-4">
            <div class="max-w-[1280px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-1 text-center sm:text-left">
                <p class="text-[10px] font-bold text-slate-600">Housing For All · Govt. of Haryana</p>
                <p class="text-[9px] text-slate-400">© 2026 CRID Haryana</p>
            </div>
        </footer>
    </div>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (menuToggle && sidebar && overlay) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        const ppNavToggle = document.getElementById('ppNavToggle');
        const ppNavSubmenu = document.getElementById('ppNavSubmenu');
        const ppNavChevron = document.getElementById('ppNavChevron');

        if (ppNavToggle && ppNavSubmenu) {
            ppNavToggle.addEventListener('click', () => {
                const isOpen = !ppNavSubmenu.classList.contains('hidden');
                ppNavSubmenu.classList.toggle('hidden');
                ppNavToggle.classList.toggle('pp-nav-group-open', !isOpen);
                ppNavToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                if (ppNavChevron) {
                    ppNavChevron.classList.toggle('rotate-180', !isOpen);
                }
            });
        }
    </script>

    @include('partials.mmsay.citizen-swal')
    @stack('scripts')
    @include('partials.mmsay.citizen-toast')
    @include('partials.global-loader')
</body>
</html>
