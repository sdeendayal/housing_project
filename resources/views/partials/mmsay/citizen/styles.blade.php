<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    *, body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    }

    body {
        font-size: 12px;
        background: #f4f6fb;
    }

    .material-symbols-outlined { font-size: 18px; }

    .sidebar-v2 {
        background: #fff;
        border-right: 1px solid #e8ecf4;
        box-shadow: 2px 0 24px rgba(99,102,241,0.05);
    }

    .sidebar-brand-icon {
        width: 36px; height: 36px;
        border-radius: 11px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        box-shadow: 0 4px 14px rgba(99,102,241,0.3);
    }

    .nav-v2 {
        display: flex; align-items: center; gap: 9px;
        padding: 7px 10px; border-radius: 10px;
        margin-bottom: 2px; font-size: 12px; font-weight: 600;
        color: #64748b; transition: all 0.18s ease;
        text-decoration: none;
    }

    .nav-v2:hover { background: #f1f5f9; color: #334155; }
    .nav-v2.active {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 14px rgba(99,102,241,0.35);
    }

    .nav-v2.active .nav-v2-icon { background: rgba(255,255,255,0.2); color: #fff; }

    .nav-v2-icon {
        width: 28px; height: 28px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        background: #f1f5f9; color: #94a3b8; flex-shrink: 0;
    }

    .pp-nav-group .nav-v2.pp-nav-group-open {
        background: #f1f5f9;
        color: #334155;
    }

    .pp-nav-submenu {
        overflow: hidden;
    }

    .pp-nav-submenu.hidden {
        display: none;
    }

    .sidebar-user-v2 {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .header-v2 {
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #e8ecf4;
    }

    .main-v2 {
        background-color: #f4f6fb;
        background-image:
            radial-gradient(circle at 15% 20%, rgba(99,102,241,0.06) 0%, transparent 40%),
            radial-gradient(circle at 85% 10%, rgba(14,165,233,0.05) 0%, transparent 35%);
    }

    .bento {
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.8);
    }

    .bento-white {
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 6px 20px rgba(99,102,241,0.06);
    }

    .bento-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 55%, #6d28d9 100%);
        box-shadow: 0 8px 28px rgba(102,126,234,0.28);
        position: relative; overflow: hidden;
    }

    .bento-green  { background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
    .bento-red    { background: linear-gradient(135deg, #dc2626, #f97316); color: #fff; }
    .bento-blue   { background: linear-gradient(135deg, #2563eb, #0ea5e9); color: #fff; }
    .bento-amber  { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; }

    .stat-chip {
        padding: 8px 10px;
        border-radius: 11px;
        display: flex; align-items: center; gap: 8px;
        min-width: 0;
    }

    .stat-chip-icon {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .donut {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }

    .donut-inner {
        width: 58px; height: 58px;
        border-radius: 50%;
        background: #fff;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
    }

    .tl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
    .tl-wrap::-webkit-scrollbar { display: none; }

    .tl-steps-row {
        display: flex; justify-content: space-between; gap: 4px;
        min-width: min(100%, 520px);
        position: relative; padding-top: 2px;
    }

    .tl-step { display: flex; flex-direction: column; align-items: center; gap: 3px; flex: 1; min-width: 56px; }

    .tl-dot {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        z-index: 2; flex-shrink: 0;
    }

    .tl-dot.done { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
    .tl-dot.active {
        background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
    }
    .tl-dot.wait { background: #f1f5f9; color: #cbd5e1; border: 1.5px dashed #e2e8f0; }

    .tl-line {
        position: absolute; top: 14px; left: 5%; right: 5%;
        height: 2px; background: #e2e8f0; border-radius: 99px;
    }

    .tl-line-fill {
        height: 100%; border-radius: 99px;
        background: linear-gradient(90deg, #10b981, #6366f1);
    }

    .action-tile {
        border-radius: 12px; padding: 10px 12px;
        display: flex; align-items: center; gap: 10px;
        text-decoration: none; color: inherit;
        transition: transform 0.18s ease;
        border: 1px solid transparent;
    }

    .action-tile:hover { transform: translateY(-2px); }
    .action-tile--profile { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-color: #bfdbfe; }
    .action-tile--pay     { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; }
    .action-tile--status  { background: linear-gradient(135deg, #fff7ed, #ffedd5); border-color: #fed7aa; }
    .action-tile--griev   { background: linear-gradient(135deg, #faf5ff, #f3e8ff); border-color: #e9d5ff; }

    .action-icon {
        width: 32px; height: 32px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    .btn-v2-primary {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff; font-weight: 700; font-size: 11px;
        padding: 7px 14px; border-radius: 10px;
        box-shadow: 0 4px 14px rgba(99,102,241,0.35);
        transition: all 0.18s ease; display: inline-flex;
        align-items: center; gap: 5px; text-decoration: none;
    }

    .btn-v2-primary:hover { transform: translateY(-1px); }
    .btn-v2-sm { padding: 6px 10px; font-size: 10px; border-radius: 8px; }

    .tag {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 8px; border-radius: 99px;
        font-size: 9px; font-weight: 700;
        letter-spacing: 0.03em; text-transform: uppercase;
        white-space: nowrap;
    }

    .tag-glass { background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.22); }
    .tag-gold  { background: rgba(251,191,36,0.22); color: #fde68a; border: 1px solid rgba(251,191,36,0.35); }

    .footer-v2 { background: #fff; border-top: 1px solid #e8ecf4; }

    .prog-bar { height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
    .prog-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #6366f1, #a855f7); }

    .citizen-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8ecf4;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 6px 20px rgba(99,102,241,0.06);
        overflow: hidden;
    }

    .pp-upload-zone {
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        background: #f8fafc;
        transition: border-color 0.18s ease, background 0.18s ease;
    }

    .pp-upload-zone:hover,
    .pp-upload-zone.dragover {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.05);
    }

    .pp-upload-zone.pp-upload-error {
        border-color: #dc2626;
        background: rgba(220, 38, 38, 0.06);
    }

    .pp-detail-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    @media (max-width: 767px) {
        .sidebar-v2 { width: 240px !important; }
        .hide-mobile { display: none !important; }
        .header-v2 .header-sub { display: none; }
    }

    @media (max-width: 480px) {
        .tag { font-size: 8px; padding: 2px 6px; }
        .donut { width: 68px; height: 68px; }
        .donut-inner { width: 48px; height: 48px; }
    }

    .pp-scheme-new-badge-sm {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        font-size: 8px;
        font-weight: 900;
        border-radius: 50px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        line-height: 1;
        white-space: nowrap;
        border: 2px solid #fff;
        z-index: 2;
        animation: ppNewBadgeBlinkClear 0.85s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .pp-scheme-new-badge-sm::before {
        content: '';
        position: absolute;
        inset: -5px;
        border-radius: 999px;
        border: 2px solid rgba(254, 240, 138, 0.9);
        opacity: 0;
        z-index: -1;
        animation: ppNewBadgeRingClear 0.85s ease-out infinite;
    }

    @keyframes ppNewBadgeBlinkClear {
        0%, 100% {
            background: #fef08a;
            color: #b91c1c;
            border-color: #fff;
            box-shadow: 0 0 0 2px #fff, 0 0 14px rgba(254, 240, 138, 1), 0 0 28px rgba(250, 204, 21, 0.85);
            transform: scale(1.18);
            opacity: 1;
        }
        50% {
            background: #dc2626;
            color: #fff;
            border-color: rgba(255, 255, 255, 0.55);
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.25), 0 0 10px rgba(185, 28, 28, 0.45);
            transform: scale(1);
            opacity: 0.55;
        }
    }

    @keyframes ppNewBadgeRingClear {
        0% { opacity: 1; transform: scale(0.9); }
        100% { opacity: 0; transform: scale(1.6); }
    }
</style>
