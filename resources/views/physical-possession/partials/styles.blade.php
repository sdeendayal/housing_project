<style>
:root {
    --pp-primary: #1e40af;
    --pp-primary-dark: #1e3a8a;
    --pp-accent: #f59e0b;
    --pp-success: #059669;
    --pp-danger: #dc2626;
    --pp-glass: rgba(255, 255, 255, 0.12);
    --pp-glass-border: rgba(255, 255, 255, 0.25);
    --pp-bg: #f0f4ff;
    --pp-card-bg: rgba(255, 255, 255, 0.85);
    --pp-text: #1e293b;
    --pp-text-muted: #64748b;
    --pp-sidebar: linear-gradient(180deg, #1e3a8a 0%, #312e81 100%);
}

[data-bs-theme="dark"] {
    --pp-bg: #0f172a;
    --pp-card-bg: rgba(30, 41, 59, 0.85);
    --pp-text: #f1f5f9;
    --pp-text-muted: #94a3b8;
    --pp-glass: rgba(255, 255, 255, 0.06);
    --pp-glass-border: rgba(255, 255, 255, 0.12);
}

body.pp-body {
    font-family: 'Inter', sans-serif;
    background: var(--pp-bg);
    color: var(--pp-text);
    min-height: 100vh;
    font-size: 0.875rem;
}

body.pp-body-auth {
    overflow: hidden;
    min-height: 100vh;
    height: 100vh;
}

/* Officer sidebar — white theme */
.pp-sidebar {
    width: 232px;
    height: 100vh;
    min-height: 100vh;
    background: #ffffff;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1040;
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.8125rem;
    border-right: 1px solid #e2e8f0;
    box-shadow: 2px 0 12px rgba(15, 23, 42, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

[data-bs-theme="dark"] .pp-sidebar {
    background: #1e293b;
    border-right-color: #334155;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.2);
}

.pp-sidebar-brand {
    padding: 1rem 0.85rem 0.85rem;
    border-bottom: 1px solid #e2e8f0;
    text-align: center;
    background: #f8fafc;
}

[data-bs-theme="dark"] .pp-sidebar-brand {
    background: #0f172a;
    border-bottom-color: #334155;
}

.pp-sidebar-close {
    position: absolute;
    top: 0.65rem;
    right: 0.65rem;
    width: 28px;
    height: 28px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    cursor: pointer;
    transition: background 0.15s;
}

.pp-sidebar-close:hover { background: #f1f5f9; color: #1e293b; }

.pp-sidebar-logo {
    width: 48px;
    height: 48px;
    margin: 0 auto 0.5rem;
    border-radius: 14px;
    background: linear-gradient(135deg, #1e40af, #3730a3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #fff;
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
}

.pp-sidebar-brand-text { margin-bottom: 0.5rem; }

.pp-sidebar-title {
    display: block;
    color: #0f172a;
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    line-height: 1.2;
}

[data-bs-theme="dark"] .pp-sidebar-title { color: #f1f5f9; }

.pp-sidebar-scheme {
    display: block;
    color: #64748b;
    font-size: 0.65rem;
    font-weight: 500;
    margin-top: 0.1rem;
}

.pp-sidebar-district {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.65rem;
    border-radius: 999px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    font-size: 0.68rem;
    font-weight: 600;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

[data-bs-theme="dark"] .pp-sidebar-district {
    background: rgba(30, 64, 175, 0.2);
    border-color: rgba(59, 130, 246, 0.3);
    color: #93c5fd;
}

.pp-sidebar-nav {
    padding: 0.5rem 0.55rem;
    overflow-y: auto;
    flex: 1 1 auto;
    min-height: 0;
}
.pp-sidebar-nav::-webkit-scrollbar {
    width: 4px;
}
.pp-sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.35);
    border-radius: 4px;
}
.pp-sidebar-nav::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.55);
}

.pp-sidebar-section {
    padding: 0.55rem 0.5rem 0.25rem;
    font-size: 0.6rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #94a3b8;
}

.pp-sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.45rem 0.5rem;
    margin-bottom: 0.15rem;
    border-radius: 10px;
    color: #475569;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.78rem;
    transition: background 0.15s, color 0.15s;
    border: 1px solid transparent;
}

[data-bs-theme="dark"] .pp-sidebar-link { color: #cbd5e1; }

.pp-sidebar-link:hover {
    background: #f1f5f9;
    color: #1e40af;
}

[data-bs-theme="dark"] .pp-sidebar-link:hover {
    background: #334155;
    color: #93c5fd;
}

.pp-sidebar-link.active {
    background: #eff6ff;
    color: #1e40af;
    border-color: #bfdbfe;
    box-shadow: inset 3px 0 0 #1e40af;
    font-weight: 600;
}

[data-bs-theme="dark"] .pp-sidebar-link.active {
    background: rgba(30, 64, 175, 0.2);
    color: #93c5fd;
    border-color: rgba(59, 130, 246, 0.3);
}

.pp-sidebar-link-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
    transition: background 0.15s, color 0.15s;
}

[data-bs-theme="dark"] .pp-sidebar-link-icon {
    background: #334155;
    color: #94a3b8;
}

.pp-sidebar-link.active .pp-sidebar-link-icon {
    background: #dbeafe;
    color: #1e40af;
}

[data-bs-theme="dark"] .pp-sidebar-link.active .pp-sidebar-link-icon {
    background: rgba(59, 130, 246, 0.25);
    color: #93c5fd;
}

.pp-sidebar-link-icon.success { color: #059669; background: #ecfdf5; }
.pp-sidebar-link-icon.danger { color: #dc2626; background: #fef2f2; }
.pp-sidebar-link-icon.warning { color: #d97706; background: #fffbeb; }
.pp-sidebar-link-icon.purple { color: #6d28d9; background: #ede9fe; }
.pp-sidebar-link-icon.orange { color: #ea580c; background: #ffedd5; }
.pp-sidebar-link-icon.green { color: #059669; background: #d1fae5; }
.pp-sidebar-link-icon.teal { color: #0284c7; background: #e0f2fe; }

[data-bs-theme="dark"] .pp-sidebar-link-icon.purple { color: #a78bfa; background: rgba(109, 40, 217, 0.2); }
[data-bs-theme="dark"] .pp-sidebar-link-icon.orange { color: #fb923c; background: rgba(234, 88, 12, 0.2); }
[data-bs-theme="dark"] .pp-sidebar-link-icon.green { color: #34d399; background: rgba(5, 150, 105, 0.2); }
[data-bs-theme="dark"] .pp-sidebar-link-icon.teal { color: #38bdf8; background: rgba(2, 132, 199, 0.2); }


.pp-sidebar-link-label {
    white-space: normal;
}

.pp-sidebar-foot {
    padding: 0.65rem 0.55rem 0.75rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    flex-shrink: 0;
    margin-top: auto;
}

[data-bs-theme="dark"] .pp-sidebar-foot {
    background: #0f172a;
    border-top-color: #334155;
}

.pp-sidebar-user {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem 0.4rem;
    margin-bottom: 0.45rem;
}

.pp-sidebar-avatar {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #1e40af, #4f46e5);
    color: #fff;
    font-size: 0.85rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(30, 64, 175, 0.2);
}

.pp-sidebar-user-info {
    min-width: 0;
    line-height: 1.25;
}

.pp-sidebar-user-info strong {
    display: block;
    color: #0f172a;
    font-size: 0.72rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

[data-bs-theme="dark"] .pp-sidebar-user-info strong { color: #f1f5f9; }

.pp-sidebar-user-info small {
    color: #64748b;
    font-size: 0.62rem;
}

.pp-sidebar-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    width: 100%;
    padding: 0.4rem 0.65rem;
    border-radius: 8px;
    background: #fff;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
}

.pp-sidebar-logout:hover {
    background: #fef2f2;
    color: #b91c1c;
}

[data-bs-theme="dark"] .pp-sidebar-logout {
    background: rgba(239, 68, 68, 0.1);
    border-color: rgba(248, 113, 113, 0.3);
    color: #fca5a5;
}

.pp-main {
    margin-left: 232px;
    min-height: 100vh;
    padding: 0.75rem 1rem 1rem;
    max-width: 100%;
}

.pp-menu-btn {
    width: 34px;
    height: 34px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    color: #1e40af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
    transition: background 0.15s;
}
.pp-menu-btn:hover { background: #eff6ff; }

.pp-page-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.625rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.pp-page-head h1 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.pp-page-head .pp-page-sub {
    font-size: 0.75rem;
    color: var(--pp-text-muted);
}

.pp-sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 1035;
}

.pp-sidebar-overlay.show { display: block; }

@media (max-width: 1199px) {
    .pp-sidebar {
        transform: translateX(-100%);
        box-shadow: 8px 0 32px rgba(0, 0, 0, 0.35);
        width: min(280px, 88vw);
    }
    .pp-sidebar.show { transform: translateX(0); }
    .pp-main { margin-left: 0; padding: 0.625rem; }
}

/* Flat panel - no big card shadow */
.pp-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 0.625rem;
}

[data-bs-theme="dark"] .pp-panel {
    background: var(--pp-card-bg);
    border-color: var(--pp-glass-border);
}

.pp-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 8px 8px 0 0;
    font-size: 0.8125rem;
    font-weight: 600;
}

[data-bs-theme="dark"] .pp-panel-head { background: rgba(255,255,255,0.04); }

.pp-panel-body { padding: 0.625rem 0.75rem; }

.pp-panel-body.p-0 { padding: 0; }

/* Compact inline stats - replaces big cards */
.pp-stat-strip {
    display: flex;
    flex-wrap: wrap;
    gap: 0.375rem;
    margin-bottom: 0.625rem;
}

.pp-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.625rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.75rem;
    white-space: nowrap;
}

.pp-stat-chip strong {
    font-size: 0.875rem;
    font-weight: 700;
}

.pp-stat-chip.blue strong { color: #1d4ed8; }
.pp-stat-chip.orange strong { color: #d97706; }
.pp-stat-chip.green strong { color: #059669; }
.pp-stat-chip.red strong { color: #dc2626; }
.pp-stat-chip.purple strong { color: #7c3aed; }

/* Compact toolbar */
.pp-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 0.75rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 0.625rem;
    font-size: 0.75rem;
}

.pp-toolbar .pp-info-item {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding-right: 0.625rem;
    margin-right: 0.625rem;
    border-right: 1px solid #e2e8f0;
}

.pp-toolbar .pp-info-item:last-of-type { border-right: none; }

.pp-btn-sm-compact {
    padding: 0.25rem 0.625rem;
    font-size: 0.75rem;
    border-radius: 6px;
    font-weight: 600;
}

.pp-btn-primary {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 0.375rem 0.875rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    transition: opacity 0.2s;
}

.pp-btn-primary:hover { opacity: 0.92; color: #fff; }

.pp-btn-primary.btn-lg { padding: 0.5rem 1rem; font-size: 0.875rem; }

/* Compact table */
.pp-table { font-size: 0.8125rem; margin: 0; }

.pp-table thead th {
    background: #1e40af;
    color: #fff;
    font-weight: 600;
    border: none;
    padding: 0.4rem 0.625rem;
    font-size: 0.75rem;
    white-space: nowrap;
}

.pp-table tbody td {
    padding: 0.4rem 0.625rem;
    vertical-align: middle;
}

.pp-table .badge { font-size: 0.6875rem; font-weight: 600; }

.pp-empty {
    text-align: center;
    padding: 1.25rem 0.75rem;
    color: var(--pp-text-muted);
    font-size: 0.8125rem;
}

.pp-empty i { font-size: 1.5rem; display: block; margin-bottom: 0.375rem; opacity: 0.5; }

/* Legacy glass card - keep flat for dashboard */
.pp-glass-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: none;
}

.pp-glass-card:hover { transform: none; box-shadow: none; }

.pp-upload-zone {
    border: 1px dashed #cbd5e1;
    border-radius: 6px;
    padding: 0.75rem;
    text-align: center;
    cursor: pointer;
    font-size: 0.8125rem;
    background: #f8fafc;
}

.pp-upload-zone i { font-size: 1.25rem !important; }

.pp-upload-zone:hover,
.pp-upload-zone.dragover {
    border-color: var(--pp-primary);
    background: rgba(30, 64, 175, 0.04);
}

.pp-detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.pp-detail-grid .label { color: var(--pp-text-muted); font-size: 0.75rem; }

.pp-detail-grid .col-span-2 { grid-column: 1 / -1; }

.pp-chart-wrap { height: 180px; position: relative; }

.pp-timeline { position: relative; padding-left: 1.25rem; font-size: 0.8125rem; }
.pp-timeline::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.pp-timeline-item { position: relative; padding-bottom: 0.625rem; }
.pp-timeline-item::before {
    content: '';
    position: absolute;
    left: -1rem;
    top: 4px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--pp-primary);
}

.pp-new-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: linear-gradient(135deg, #ef4444, #dc2626, #b91c1c);
    color: #fff;
    font-size: 0.85rem;
    font-weight: 700;
    border-radius: 50px;
    letter-spacing: 0.5px;
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.6), 0 0 40px rgba(239, 68, 68, 0.3);
    animation: pp-blink-pulse 1.5s ease-in-out infinite;
    text-transform: uppercase;
}

@keyframes pp-blink-pulse {
    0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 20px rgba(239,68,68,0.6); }
    50% { opacity: 0.85; transform: scale(1.03); box-shadow: 0 0 35px rgba(239,68,68,0.9), 0 0 60px rgba(239,68,68,0.4); }
}

.pp-glass-card {
    background: var(--pp-card-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--pp-glass-border);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pp-glass-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.pp-hero {
    min-height: 100vh;
    background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 40%, #4f46e5 70%, #6366f1 100%);
    position: relative;
    overflow: hidden;
}

.pp-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.pp-floating-shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    animation: pp-float 6s ease-in-out infinite;
}

@keyframes pp-float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.pp-btn-primary {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 12px 28px;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
}

.pp-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
    color: #fff;
}

.pp-btn-outline {
    background: rgba(255,255,255,0.15);
    border: 2px solid rgba(255,255,255,0.5);
    color: #fff;
    font-weight: 600;
    padding: 12px 28px;
    border-radius: 12px;
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
}

.pp-btn-outline:hover {
    background: rgba(255,255,255,0.25);
    color: #fff;
    transform: translateY(-2px);
}

/* Hide legacy big stat cards */
.pp-stat-card { display: none; }

.pp-login-card { max-width: 440px; margin: 0 auto; }

.pp-loading {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.pp-loading.show { display: flex; }

@media print {
    .pp-sidebar, .pp-no-print, .navbar { display: none !important; }
    .pp-main { margin-left: 0 !important; }
}

.pp-table thead th {
    background: linear-gradient(135deg, #1e40af, #3730a3);
    color: #fff;
    font-weight: 600;
    border: none;
}

/* duplicate pp-table removed - defined above in compact section */

/* Auth login — clean glass card (cashaward-style) */
.pp-auth-page {
    position: relative;
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    font-size: 0.8125rem;
}

.pp-auth-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
    overflow: hidden;
}

.pp-auth-bg img {
    position: absolute;
    top: -4%;
    left: 0;
    width: 100%;
    height: 110%;
    object-fit: cover;
    object-position: 62% 38%;
    display: block;
    filter: blur(1.5px);
    transform: scale(1.02);
}

.pp-auth-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.15) 0%,
        rgba(255, 255, 255, 0.05) 42%,
        rgba(15, 23, 42, 0.12) 100%
    );
    pointer-events: none;
}

.pp-auth-inner {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    height: 100%;
    padding: max(0.75rem, env(safe-area-inset-top))
        max(1rem, env(safe-area-inset-right))
        max(0.75rem, env(safe-area-inset-bottom))
        max(clamp(1rem, 4vw, 3rem), env(safe-area-inset-left));
}

.pp-auth-card {
    width: 100%;
    max-width: min(340px, calc(100vw - 2rem));
    max-height: calc(100dvh - 1.5rem);
    overflow-y: auto;
    padding: 1rem 1.1rem 0.95rem;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.55);
    background: rgba(255, 255, 255, 0.72);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.14);
}

.pp-auth-card__head {
    text-align: center;
    margin-bottom: 0.65rem;
}

.pp-auth-card__logo {
    width: 46px;
    height: 46px;
    margin: 0 auto 0.45rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: 50%;
    padding: 4px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}

.pp-auth-card__logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.pp-auth-card__dept {
    color: #0f4c81;
    font-size: 0.78rem;
    font-weight: 700;
    line-height: 1.25;
}

.pp-auth-card__scheme {
    color: #475569;
    font-size: 0.68rem;
    line-height: 1.35;
    margin-top: 0.1rem;
}

.pp-auth-chip {
    display: inline-block;
    margin-top: 0.4rem;
    padding: 0.15rem 0.55rem;
    border-radius: 999px;
    background: rgba(30, 64, 175, 0.1);
    color: #1e40af;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.pp-auth-card__title {
    margin: 0.45rem 0 0;
    color: #005bac;
    font-size: 0.95rem;
    font-weight: 700;
}

.pp-auth-card__hint {
    margin-top: 0.15rem;
    color: #64748b;
    font-size: 0.68rem;
    line-height: 1.35;
}

.pp-auth-card__body .pp-auth-label {
    color: #1e293b;
    font-size: 0.76rem;
    font-weight: 600;
    margin-bottom: 0.2rem;
}

.pp-auth-card__body .form-control,
.pp-auth-card__body .input-group-text {
    font-size: 0.84rem;
    min-height: 2.15rem;
    border: 1px solid rgba(148, 163, 184, 0.55);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.82);
}

.pp-auth-card__body .input-group-text {
    padding: 0.3rem 0.55rem;
    color: #475569;
    background: rgba(248, 250, 252, 0.95);
}

.pp-auth-card__body .form-control:focus {
    border-color: #005bac;
    box-shadow: 0 0 0 2px rgba(0, 91, 172, 0.14);
    background: rgba(255, 255, 255, 0.95);
}

.pp-auth-card__body .field {
    margin-bottom: 0.45rem;
}

.pp-auth-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    margin-top: 0.35rem;
    padding: 0.42rem 1rem;
    border: none;
    border-radius: 8px;
    background: #0f4c81;
    color: #fff;
    font-size: 0.84rem;
    font-weight: 600;
    transition: background 0.2s ease;
}

.pp-auth-btn:hover {
    background: #0a3d6b;
    color: #fff;
}

.pp-auth-btn--officer {
    background: #5b21b6;
}

.pp-auth-btn--officer:hover {
    background: #4c1d95;
}

.pp-captcha-row {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.35rem;
}

.pp-captcha-box {
    flex: 1;
    min-height: 38px;
    border-radius: 6px;
    border: 1px solid rgba(148, 163, 184, 0.45);
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    font-weight: 800;
    font-style: italic;
    letter-spacing: 5px;
    color: #0f172a;
    user-select: none;
}

.pp-captcha-refresh {
    width: 38px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(148, 163, 184, 0.55);
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.85);
    color: #0f4c81;
}

.pp-captcha-refresh:hover {
    background: #fff;
    color: #005bac;
}

.pp-otp-input {
    letter-spacing: 6px;
    font-weight: 700;
    font-size: 1rem !important;
}

.pp-auth-card__foot {
    margin-top: 0.65rem;
    padding-top: 0.55rem;
    border-top: 1px solid rgba(148, 163, 184, 0.35);
    text-align: center;
}

.pp-auth-features {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.5rem 0.75rem;
    margin-bottom: 0.45rem;
    color: #64748b;
    font-size: 0.65rem;
    font-weight: 600;
}

.pp-auth-features span {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
}

.pp-auth-features i {
    color: #0f4c81;
    font-size: 0.72rem;
}

.pp-auth-back {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 600;
    text-decoration: none;
}

.pp-auth-back:hover {
    color: #0f4c81;
}

.pp-auth-card--officer .pp-auth-card__title {
    color: #5b21b6;
}

.pp-auth-card--officer .pp-auth-chip {
    background: rgba(91, 33, 182, 0.1);
    color: #5b21b6;
}

@media (max-width: 767px) {
    .pp-auth-inner {
        justify-content: center;
    }

    .pp-auth-bg img {
        object-position: 68% 34%;
    }
}

@media (max-height: 620px) {
    .pp-auth-card {
        padding: 0.75rem 0.9rem;
    }

    .pp-auth-card__logo {
        width: 38px;
        height: 38px;
    }

    .pp-auth-card__body .field {
        margin-bottom: 0.35rem;
    }
}

/* CM banner photo on scheme sections */
.pp-cm-banner-photo {
    max-width: 320px;
    width: 100%;
    height: auto;
    border-radius: 10px;
    background: #fff;
    padding: 6px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}
@media (min-width: 768px) {
    .pp-cm-banner-photo { max-width: 380px; }
}
</style>
