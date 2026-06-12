<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Officer Dashboard') - Physical Possession</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    @include('physical-possession.partials.styles')
    @stack('styles')
</head>
<body class="pp-body">
    @php $officer = Auth::user(); @endphp

    <div class="pp-sidebar-overlay" id="ppSidebarOverlay" onclick="ppToggleSidebar()"></div>

    <nav class="pp-sidebar d-flex flex-column" id="ppSidebar">
        <div class="pp-sidebar-brand">
            <button type="button" class="pp-sidebar-close d-lg-none" onclick="ppToggleSidebar()" aria-label="Close menu">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="pp-sidebar-logo">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="pp-sidebar-brand-text">
                <span class="pp-sidebar-title">Officer Panel</span>
                <span class="pp-sidebar-scheme">Physical Possession</span>
            </div>
            <div class="pp-sidebar-district">
                <i class="bi bi-geo-alt-fill"></i> {{ $officer->district_name }}
            </div>
        </div>

        <div class="pp-sidebar-nav flex-grow-1">
            <div class="pp-sidebar-section">Main</div>
            <a href="{{ route('pp.officer.dashboard') }}" class="pp-sidebar-link {{ request()->routeIs('pp.officer.dashboard') ? 'active' : '' }}">
                <span class="pp-sidebar-link-icon"><i class="bi bi-speedometer2"></i></span>
                <span class="pp-sidebar-link-label">Dashboard</span>
            </a>

            <div class="pp-sidebar-section">Applications</div>
            <a href="{{ route('pp.officer.applications') }}" class="pp-sidebar-link {{ request()->routeIs('pp.officer.applications', 'pp.officer.application.show') ? 'active' : '' }}">
                <span class="pp-sidebar-link-icon"><i class="bi bi-folder2-open"></i></span>
                <span class="pp-sidebar-link-label">All Applications</span>
            </a>
            <a href="{{ route('pp.officer.applications.approved') }}" class="pp-sidebar-link {{ request()->routeIs('pp.officer.applications.approved') ? 'active' : '' }}">
                <span class="pp-sidebar-link-icon success"><i class="bi bi-check-circle"></i></span>
                <span class="pp-sidebar-link-label">Approved</span>
            </a>
            <a href="{{ route('pp.officer.applications.rejected') }}" class="pp-sidebar-link {{ request()->routeIs('pp.officer.applications.rejected') ? 'active' : '' }}">
                <span class="pp-sidebar-link-icon danger"><i class="bi bi-x-circle"></i></span>
                <span class="pp-sidebar-link-label">Rejected</span>
            </a>

            <div class="pp-sidebar-section">Manage</div>
            <a href="{{ route('pp.officer.reports') }}" class="pp-sidebar-link {{ request()->routeIs('pp.officer.reports') ? 'active' : '' }}">
                <span class="pp-sidebar-link-icon"><i class="bi bi-bar-chart-line"></i></span>
                <span class="pp-sidebar-link-label">Reports</span>
            </a>
        </div>

        <div class="pp-sidebar-foot">
            <div class="pp-sidebar-user">
                <div class="pp-sidebar-avatar">{{ strtoupper(substr($officer->name, 0, 1)) }}</div>
                <div class="pp-sidebar-user-info">
                    <strong>{{ $officer->name }}</strong>
                    <small>District Officer</small>
                </div>
            </div>
            <a href="{{ route('pp.officer.logout') }}" class="pp-sidebar-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </nav>

    <div class="pp-main">
        <div class="pp-page-head pp-no-print">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <button type="button" class="pp-menu-btn d-lg-none" onclick="ppToggleSidebar()" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="min-w-0">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <div class="pp-page-sub text-truncate">{{ $officer->name }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 shrink-0">
            <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="ppToggleTheme()" title="Toggle theme">
                <i class="bi bi-moon-stars"></i>
            </button>
            <a href="{{ route('pp.officer.logout') }}" class="btn btn-sm btn-outline-danger py-0 px-2 d-flex align-items-center gap-1" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </a>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @include('physical-possession.partials.scripts')
    @stack('scripts')
</body>
</html>
