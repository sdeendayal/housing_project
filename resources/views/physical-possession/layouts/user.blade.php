<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'User Dashboard') - Physical Possession</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @include('physical-possession.partials.styles')
    @stack('styles')
</head>
<body class="pp-body">
    <div class="pp-sidebar-overlay" id="ppSidebarOverlay" onclick="ppToggleSidebar()"></div>

    <nav class="pp-sidebar d-flex flex-column" id="ppSidebar">
        <div class="pp-sidebar-brand text-white border-bottom border-white border-opacity-10">
            <h6 class="fw-bold"><i class="bi bi-building me-1"></i>PP Portal</h6>
            <small class="opacity-75">Citizen</small>
        </div>
        <div class="flex-grow-1 py-2">
            <a href="{{ route('pp.user.dashboard') }}" class="nav-link {{ request()->routeIs('pp.user.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
            <a href="{{ route('pp.user.apply') }}" class="nav-link {{ request()->routeIs('pp.user.apply*') ? 'active' : '' }}">
                <i class="bi bi-pencil-square me-1"></i> Apply
            </a>
            <a href="{{ route('pp.user.applications') }}" class="nav-link {{ request()->routeIs('pp.user.applications') || request()->routeIs('pp.user.application.*') ? 'active' : '' }}">
                <i class="bi bi-list-check me-1"></i> My Applications
            </a>
            <a href="{{ route('pp.user.profile') }}" class="nav-link {{ request()->routeIs('pp.user.profile') ? 'active' : '' }}">
                <i class="bi bi-person me-1"></i> Profile
            </a>
        </div>
        <div class="py-2 border-top border-white border-opacity-10">
            <a href="{{ route('pp.user.logout') }}" class="nav-link text-danger py-2">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </a>
        </div>
    </nav>

    <div class="pp-main">
        <div class="pp-page-head pp-no-print">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <button class="btn btn-sm btn-outline-primary d-xl-none py-0 px-2" onclick="ppToggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="min-w-0">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <div class="pp-page-sub text-truncate">Welcome, {{ Auth::user()->name }}</div>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="ppToggleTheme()" title="Toggle theme">
                <i class="bi bi-moon-stars"></i>
            </button>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    @include('physical-possession.partials.scripts')
    @stack('scripts')
</body>
</html>
