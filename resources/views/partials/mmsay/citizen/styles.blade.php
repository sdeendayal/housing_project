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
        z-index: 60 !important;
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
        min-height: 88px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
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

    .pp-upload-zone.pp-upload-zone-signed {
        border: 2px dashed #10b981;
        background: linear-gradient(180deg, #ecfdf5 0%, #f8fafc 100%);
        min-height: 110px;
    }

    .pp-upload-zone.pp-upload-zone-signed:hover,
    .pp-upload-zone.pp-upload-zone-signed.dragover {
        border-color: #059669;
        background: #d1fae5;
    }

    .pp-cert-workflow {
        border: 1px solid #c7d2fe;
        border-radius: 14px;
        background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 55%, #ecfdf5 100%);
        overflow: hidden;
    }

    .pp-cert-workflow-head {
        padding: 10px 12px;
        border-bottom: 1px solid rgba(99, 102, 241, 0.15);
        background: rgba(255, 255, 255, 0.65);
    }

    .pp-cert-steps {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 12px;
    }

    @media (min-width: 768px) {
        .pp-cert-steps {
            grid-template-columns: 1fr auto 1fr auto 1.2fr;
            align-items: stretch;
            gap: 8px;
        }
    }

    .pp-cert-step {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-height: 100%;
    }

    .pp-cert-step-download {
        border-color: #bfdbfe;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.08);
    }

    .pp-cert-step-sign {
        border-color: #fde68a;
        background: #fffbeb;
    }

    .pp-cert-step-upload {
        border-color: #6ee7b7;
        background: #fff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.1);
    }

    .pp-step-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 2px 8px;
        border-radius: 999px;
        width: fit-content;
    }

    .pp-step-badge-blue { background: #dbeafe; color: #1d4ed8; }
    .pp-step-badge-amber { background: #fef3c7; color: #b45309; }
    .pp-step-badge-green { background: #d1fae5; color: #047857; }

    .pp-cert-step-title {
        font-size: 11px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
    }

    .pp-cert-step-desc {
        font-size: 10px;
        color: #64748b;
        margin: 0;
        line-height: 1.45;
        flex: 1;
    }

    .pp-cert-arrow {
        display: none;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 20px;
        padding: 0 2px;
    }

    @media (min-width: 768px) {
        .pp-cert-arrow { display: flex; }
    }

    .pp-cert-arrow-mobile {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        padding: 2px 0;
    }

    @media (min-width: 768px) {
        .pp-cert-arrow-mobile { display: none; }
    }

    .pp-cert-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        border: 1px solid transparent;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .pp-cert-action-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .pp-cert-action-view {
        background: #4f46e5;
        color: #fff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .pp-cert-action-view:hover { color: #fff; background: #4338ca; }

    .pp-cert-action-download {
        background: #059669;
        color: #fff;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
    }

    .pp-cert-action-download:hover { color: #fff; background: #047857; }

    /* Pre-filled possession certificate preview on apply page */
    .pp-cert-preview-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        overflow: hidden;
    }

    .pp-cert-preview-head {
        padding: 8px 12px;
        background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
        border-bottom: 1px solid #e2e8f0;
    }

    .pp-cert-preview-paper {
        max-height: 380px;
        overflow-y: auto;
        background: #fff;
        margin: 10px 12px 0;
        padding: 1.25rem 1.5rem;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 8px rgba(15, 23, 42, 0.04);
        font-size: 12px;
        line-height: 1.6;
        color: #111;
    }

    .pp-cert-preview-paper::-webkit-scrollbar { width: 5px; }
    .pp-cert-preview-paper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .pp-cert-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 10px 12px 12px;
        padding: 10px 12px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
    }

    .pp-cert-action-bar--done {
        justify-content: center;
    }

    .pp-cert-action-hint {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .pp-cert-action-hint-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .pp-cert-action-hint-icon .material-symbols-outlined { font-size: 17px; }

    .pp-cert-action-hint-title {
        font-size: 10px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        line-height: 1.3;
    }

    .pp-cert-action-hint-sub {
        font-size: 9px;
        color: #64748b;
        margin: 1px 0 0;
        line-height: 1.3;
    }

    .pp-cert-verify-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 7px 16px;
        border: none;
        border-radius: 999px;
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.02em;
        cursor: pointer;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.28);
        transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
    }

    .pp-cert-verify-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
    }

    .pp-cert-verify-btn:active { transform: translateY(0); }

    .pp-cert-verify-btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        transform: none;
    }

    .pp-cert-verify-btn .material-symbols-outlined { font-size: 15px; }

    .pp-cert-verified-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 1px solid #a7f3d0;
        flex-shrink: 0;
    }

    .pp-cert-verified-pill .material-symbols-outlined {
        font-size: 16px;
        color: #059669;
    }

    .pp-cert-verified-pill-text {
        font-size: 9px;
        font-weight: 800;
        color: #047857;
        margin: 0;
        line-height: 1.2;
    }

    .pp-cert-verified-pill-sub {
        font-size: 8px;
        color: #059669;
        margin: 0;
        line-height: 1.2;
    }

    @media (max-width: 480px) {
        .pp-cert-action-bar {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .pp-cert-action-hint {
            justify-content: center;
        }

        .pp-cert-verify-btn,
        .pp-cert-verified-pill {
            width: 100%;
        }
    }

    .pp-form-line {
        border-bottom: 1px solid #111;
        display: inline-block;
        min-width: 40px;
        padding: 0 3px 1px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .pp-form-body { text-align: justify; margin: 1rem 0 1.25rem; }

    .pp-form-thanks { text-align: center; margin: 1rem 0; }

    .pp-form-signature { text-align: right; margin-top: 0.5rem; }

    .pp-form-sign-name {
        margin-top: 2rem;
        font-weight: 700;
        text-transform: uppercase;
        border-top: 1px solid #111;
        display: inline-block;
        min-width: 160px;
        padding-top: 4px;
    }

    .pp-form-meta {
        margin-top: 1.5rem;
        font-size: 9px;
        color: #64748b;
        border-top: 1px dashed #cbd5e1;
        padding-top: 0.5rem;
    }

    /* Allotment letter — original HTML template style */
    .pp-allotment-letter-wrap { padding: 4px; }
    .pp-allotment-container {
        border: 5px solid orange;
        padding: 14px 16px;
        background: #fff;
        border-radius: 2px;
    }
    .pp-allotment-container + .pp-allotment-container {
        margin-top: 20px;
        padding-top: 18px;
    }
    .pp-allotment-logo-img { display: block; margin: 0 auto 8px; }
    .pp-allotment-dept-title {
        font-size: 15px;
        font-weight: 800;
        color: #111;
        margin: 0 0 6px;
        text-align: center;
    }
    .pp-allotment-green-badge {
        display: inline-block;
        background: #198754;
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        padding: 4px 14px;
        border-radius: 4px;
        margin: 4px 0;
        text-align: center;
    }
    .pp-allotment-scheme-title {
        font-size: 14px;
        font-weight: 800;
        color: #d97706;
        margin: 6px 0;
        text-align: center;
    }
    .pp-allotment-intro-text {
        font-size: 10px;
        color: #333;
        text-align: center;
        margin: 0 0 10px;
        line-height: 1.5;
    }
    .pp-allotment-data-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        font-size: 10px;
    }
    .pp-allotment-data-table th,
    .pp-allotment-data-table td {
        border: 1px solid #dee2e6;
        padding: 6px 8px;
        text-align: left;
        vertical-align: top;
    }
    .pp-allotment-data-table th {
        color: rgb(0, 112, 192);
        font-weight: 700;
        width: 42%;
        background: #f8f9fa;
    }
    .pp-allotment-data-table tr:nth-child(even) td { background: #f8f9fa; }
    .pp-allotment-qr-section {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
        padding-top: 8px;
    }
    .pp-allotment-qr-box {
        border: 1px solid #dee2e6;
        padding: 4px;
        background: #fff;
        flex-shrink: 0;
    }
    .pp-allotment-qr-label {
        font-size: 10px;
        color: #333;
        margin: 0;
        line-height: 1.4;
    }
    .pp-allotment-sign-note {
        font-size: 9px;
        text-align: right;
        margin: 10px 0 0;
        color: #333;
    }
    .pp-allotment-terms-star { color: #dc3545; }
    .pp-allotment-terms-heading {
        font-size: 13px;
        font-weight: 800;
        text-align: center;
        margin: 0 0 10px;
        color: #111;
    }
    .pp-allotment-numbered-list {
        counter-reset: allot-counter;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .pp-allotment-numbered-list li {
        position: relative;
        padding-left: 1.6em;
        margin-bottom: 7px;
        font-size: 9px;
        line-height: 1.45;
        color: #222;
    }
    .pp-allotment-numbered-list li::before {
        counter-increment: allot-counter;
        content: counter(allot-counter) ". ";
        position: absolute;
        left: 0;
        top: 0;
        font-weight: 800;
    }

    .pp-allotment-preview-paper {
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
        margin: 10px 12px 0;
        max-height: none;
        overflow-y: visible;
    }

    .pp-upload-here-label {
        font-size: 11px;
        font-weight: 800;
        color: #047857;
        margin: 0;
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

    .citizen-payment-banner {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.06);
        margin-bottom: 2px;
    }

    .citizen-payment-banner--success {
        border: 1px solid #6ee7b7;
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #fff 55%);
    }

    .citizen-payment-banner--warning {
        border: 1px solid #fcd34d;
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fff 55%);
    }

    .citizen-payment-banner__icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .citizen-payment-banner--success .citizen-payment-banner__icon {
        background: #d1fae5;
        color: #059669;
    }

    .citizen-payment-banner--warning .citizen-payment-banner__icon {
        background: #fef3c7;
        color: #d97706;
    }

    .citizen-payment-banner__body {
        flex: 1;
        min-width: 0;
    }

    .citizen-payment-banner__title {
        font-size: 12px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 4px;
    }

    .citizen-payment-banner__message {
        font-size: 11px;
        font-weight: 500;
        color: #475569;
        line-height: 1.5;
        margin: 0 0 8px;
    }

    .citizen-payment-banner__stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        font-size: 10px;
        color: #64748b;
    }

    .citizen-payment-banner__stats strong {
        color: #334155;
        font-weight: 700;
    }

    .citizen-payment-banner__action {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        align-self: center;
        white-space: nowrap;
    }

    .citizen-payment-banner__action:hover {
        color: #fff;
        opacity: 0.95;
    }

    .nav-v2--locked {
        opacity: 0.55;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Sidebar transitions and collapse styling */
    #sidebar {
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    #mainContentWrapper {
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    #sidebarCollapseToggle {
        position: absolute !important;
        right: -12px !important;
        top: 20px !important;
        width: 24px !important;
        height: 24px !important;
        border-radius: 50% !important;
        background: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #334155 !important;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        cursor: pointer !important;
        z-index: 100 !important;
        display: none;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
    }
    #sidebarCollapseToggle:hover {
        color: #4f46e5 !important;
        background: #f8fafc !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
        transform: scale(1.08) !important;
    }
    #sidebarCollapseToggle span {
        font-size: 16px !important;
        font-weight: bold !important;
        transition: transform 0.3s ease !important;
    }

    @media (min-width: 768px) {
        #sidebarCollapseToggle {
            display: flex !important;
        }
        .sidebar-collapsed #sidebar {
            width: 64px !important;
        }
        .sidebar-collapsed #mainContentWrapper {
            margin-left: 64px !important;
        }
        .sidebar-collapsed #sidebar .nav-text,
        .sidebar-collapsed #sidebar .sidebar-brand-text,
        .sidebar-collapsed #sidebar .sidebar-user-text,
        .sidebar-collapsed #sidebar .sidebar-user-logout,
        .sidebar-collapsed #sidebar .menu-heading {
            display: none !important;
        }
        .sidebar-collapsed #sidebar .px-3.5 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            display: flex;
            justify-content: center;
        }
        .sidebar-collapsed #sidebar .sidebar-brand-icon {
            margin: 0 auto;
        }
        .sidebar-collapsed #sidebar .sidebar-user-v2 {
            justify-content: center;
            padding: 8px 4px;
        }
        .sidebar-collapsed #sidebar .pp-nav-submenu .nav-v2 {
            padding-left: 8px;
        }
        .sidebar-collapsed #sidebar .flex-1.overflow-y-auto {
            padding-left: 8px;
            padding-right: 8px;
        }
        .sidebar-collapsed #sidebar .nav-v2 {
            justify-content: center;
            padding: 8px;
            gap: 0;
        }
        .sidebar-collapsed #sidebarCollapseToggle span {
            transform: rotate(180deg);
        }
        .sidebar-collapsed #sidebar .pp-nav-submenu {
            margin-top: 4px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 4px;
        }
    }
</style>
