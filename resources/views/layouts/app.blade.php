<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Tata Usaha & Dokumen Digital') — BWS Kalimantan I</title>

    <!-- Favicon Resmi Kementerian PU -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_pu.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_pu.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_pu.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            /* ── Palet Warna BWS / PU Navy & Gold ── */
            --pu-navy:        #13243d;
            --pu-navy-dark:   #0b1626;
            --pu-navy-light:  #1f3659;
            --pu-navy-50:     #eef2f8;
            --pu-blue:        #1d4ed8;
            --pu-blue-light:  #eff6ff;
            --pu-gold:        #f59e0b;
            --pu-gold-dark:   #d97706;
            --pu-gold-light:  #fef3c7;

            /* ── Permukaan & Latar ── */
            --bg-body:        #f0f2f5;
            --bg-surface:     #ffffff;
            --bg-subtle:      #f8f9fb;
            --border:         #e8eaed;
            --border-hover:   #c9cdd4;

            /* ── Tipografi ── */
            --text-main:      #111827;
            --text-muted:     #6b7280;
            --text-subtle:    #9ca3af;

            /* ── Status ── */
            --success:        #10b981;
            --success-bg:     #ecfdf5;
            --danger:         #ef4444;
            --danger-bg:      #fef2f2;
            --warning:        #f59e0b;
            --warning-bg:     #fffbeb;

            /* ── Dimensi ── */
            --sidebar-width:  260px;
            --header-height:  68px;
            --radius-sm:      8px;
            --radius-md:      12px;
            --radius-lg:      18px;
            --radius-xl:      24px;
            --shadow-card:    0 2px 8px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
            --shadow-sm:      0 1px 3px rgba(0,0,0,0.08);
            --shadow-md:      0 4px 12px rgba(0,0,0,0.10);
            --shadow-lg:      0 8px 24px rgba(0,0,0,0.12);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════════════════
           SIDEBAR  —  Light / White
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            z-index: 60;
            transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 2px 0 12px rgba(0,0,0,0.04);
        }

        /* Brand / Logo Area */
        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 16px 0 20px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .sidebar-brand-left {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
        }
        .sidebar-toggle-btn {
            width: 30px; height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-subtle);
            color: var(--text-muted);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 13px; flex-shrink: 0;
            transition: all 0.15s;
        }
        .sidebar-toggle-btn:hover {
            background: var(--pu-navy); color: #ffffff; border-color: var(--pu-navy);
        }

        /* Floating reopen button when desktop sidebar is hidden */
        .sidebar-reopen-btn {
            display: none;
            position: fixed;
            top: 22px; left: 22px;
            z-index: 50;
            height: 38px; padding: 0 14px;
            border-radius: 50px;
            border: 1px solid var(--border);
            background: var(--bg-surface);
            color: var(--pu-navy);
            font-family: inherit;
            font-size: 13px; font-weight: 700;
            align-items: center; gap: 7px;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            transition: all 0.2s;
        }
        .sidebar-reopen-btn:hover {
            background: var(--pu-navy); color: #ffffff; border-color: var(--pu-navy);
        }

        body.sidebar-hidden .sidebar {
            transform: translateX(-100%);
        }
        body.sidebar-hidden .wrapper {
            margin-left: 0;
        }
        body.sidebar-hidden .sidebar-reopen-btn {
            display: inline-flex;
        }
        body.sidebar-hidden .content {
            padding-top: 28px;
        }
        body.sidebar-hidden .dash-header,
        body.sidebar-hidden .subpage-header {
            padding-left: 110px;
        }

        .brand-logo {
            width: 40px; height: 40px;
            background: var(--pu-navy-50);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            padding: 4px;
            border: 1.5px solid rgba(19,36,61,0.12);
            flex-shrink: 0;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; }

        .brand-text h1 {
            font-size: 13px; font-weight: 800;
            letter-spacing: -0.1px;
            color: var(--pu-navy);
            line-height: 1.2;
        }
        .brand-text span {
            font-size: 10.5px; color: var(--text-muted);
            font-weight: 500; display: block; margin-top: 1px;
        }

        /* Navigation */
        .sidebar-menu {
            flex: 1; overflow-y: auto;
            padding: 14px 10px;
        }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--border); border-radius: 4px;
        }

        .menu-category {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1.2px;
            color: var(--text-subtle);
            padding: 14px 12px 5px;
            margin-top: 4px;
        }
        .menu-category:first-child { margin-top: 0; }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: all 0.15s ease;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-item:hover {
            background: var(--bg-subtle);
            color: var(--text-main);
        }
        /* Active pill – navy background capsule with left accent */
        .nav-item.active {
            background: var(--pu-navy);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(19,36,61,0.22);
        }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; border-radius: 0 3px 3px 0;
            background: var(--pu-gold);
            display: none; /* hidden inside pill, gold is the pill border accent */
        }
        .nav-icon { font-size: 15px; width: 20px; text-align: center; flex-shrink: 0; }

        .nav-badge {
            margin-left: auto;
            background: var(--pu-navy-50);
            color: var(--pu-navy);
            padding: 1px 7px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .nav-item.active .nav-badge {
            background: rgba(255,255,255,0.2); color: #ffffff;
        }

        /* ── Sidebar Footer – User card ── */
        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sidebar-user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            background: var(--bg-subtle);
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
        }

        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--pu-navy);
            color: #ffffff; font-weight: 800; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            border: 2px solid var(--pu-gold);
        }

        .user-info { flex: 1; min-width: 0; }
        .user-info .user-name {
            font-size: 12.5px; font-weight: 700;
            color: var(--text-main);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-info .user-role {
            font-size: 11px; color: var(--text-muted);
            display: flex; align-items: center; gap: 4px;
        }
        .online-dot {
            width: 6px; height: 6px; background: var(--success);
            border-radius: 50%; box-shadow: 0 0 6px var(--success);
            display: inline-block;
        }

        .btn-logout-mini {
            background: none; border: none; cursor: pointer;
            color: var(--text-subtle); padding: 4px; border-radius: 6px;
            transition: all 0.15s; font-size: 15px; line-height: 1;
            flex-shrink: 0;
        }
        .btn-logout-mini:hover { background: #fee2e2; color: var(--danger); }

        /* ══════════════════════════════════════
           MOBILE BACKDROP
        ══════════════════════════════════════ */
        .sidebar-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(15,23,42,0.5);
            backdrop-filter: blur(3px);
            z-index: 55;
        }

        /* ══════════════════════════════════════
           MAIN WRAPPER
        ══════════════════════════════════════ */
        .wrapper {
            margin-left: var(--sidebar-width);
            flex: 1; display: flex; flex-direction: column;
            min-width: 0;
            transition: margin-left 0.25s ease;
        }

        /* ══════════════════════════════════════
           TOPBAR (Mobile-only hamburger bar)
        ══════════════════════════════════════ */
        .topbar {
            display: none;
            height: 60px;
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border);
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            position: sticky; top: 0; z-index: 40;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            gap: 12px;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }

        .mobile-hamburger {
            display: flex;
            background: var(--bg-subtle); border: 1px solid var(--border);
            color: var(--text-main);
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            align-items: center; justify-content: center;
            font-size: 17px; cursor: pointer;
            transition: all 0.15s; flex-shrink: 0;
        }
        .mobile-hamburger:hover { background: var(--border); }

        .mobile-brand-label {
            font-size: 14px;
            font-weight: 800;
            color: var(--pu-navy);
            letter-spacing: -0.2px;
        }

        /* ── Subpage Header (clean, full-width, never clipped) ── */
        .subpage-header {
            margin-bottom: 22px;
        }
        .subpage-header h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .subpage-header p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        @media print {
            .subpage-header, .sidebar-reopen-btn { display: none !important; }
        }

        /* Search bar – capsule */
        .topbar-search-wrap {
            position: relative;
            min-width: 240px;
        }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg-subtle); border: 1.5px solid var(--border);
            border-radius: 50px; padding: 6px 14px;
            font-size: 13px; color: var(--text-muted);
            transition: border-color 0.2s, background 0.2s;
        }
        .topbar-search:focus-within {
            border-color: var(--pu-navy);
            background: #ffffff;
        }
        .topbar-search input {
            border: none; background: transparent; outline: none;
            font-size: 13px; font-family: inherit; color: var(--text-main);
            width: 100%;
        }
        .topbar-search .search-shortcut {
            margin-left: auto; font-size: 10.5px;
            background: var(--border); color: var(--text-muted);
            padding: 2px 6px; border-radius: 5px; font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
        }
        .search-dropdown {
            display: none;
            position: absolute; top: calc(100% + 8px); left: 0; right: 0;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 100;
            overflow: hidden;
            max-height: 280px;
            overflow-y: auto;
        }
        .search-dropdown.show { display: block; }
        .search-result-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; text-decoration: none;
            color: var(--text-main); font-size: 13px; font-weight: 500;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.12s;
        }
        .search-result-item:last-child { border-bottom: none; }
        .search-result-item:hover { background: var(--pu-navy-50); color: var(--pu-navy); }

        .topbar-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--bg-subtle); border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; cursor: pointer; transition: all 0.15s;
            text-decoration: none; color: var(--text-muted);
        }
        .topbar-icon-btn:hover { background: var(--border); color: var(--text-main); }

        .topbar-divider { width: 1px; height: 28px; background: var(--border); }

        .topbar-user {
            display: flex; align-items: center; gap: 10px;
            padding-left: 4px;
        }
        .topbar-user-info { text-align: right; }
        .topbar-user-info .tu-name {
            font-size: 13px; font-weight: 700; color: var(--text-main);
        }
        .topbar-user-info .tu-email {
            font-size: 11px; color: var(--text-muted);
        }
        .topbar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--pu-navy); color: #ffffff;
            font-weight: 800; font-size: 12px;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--pu-gold);
            flex-shrink: 0;
        }
        .btn-topbar-logout {
            display: inline-flex; align-items: center; gap: 5px;
            background: #fff0f0; border: 1px solid #fecaca; color: #dc2626;
            padding: 6px 12px; border-radius: var(--radius-sm);
            font-size: 12px; font-weight: 700; cursor: pointer;
            transition: all 0.15s;
        }
        .btn-topbar-logout:hover { background: #fee2e2; }

        /* ── Quick Create CTA ── */
        .btn-quick-create {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--pu-navy); color: #ffffff;
            padding: 9px 18px; border-radius: 50px;
            text-decoration: none; font-size: 13px; font-weight: 600;
            transition: all 0.2s; white-space: nowrap;
            box-shadow: 0 3px 10px rgba(19,36,61,0.22);
        }
        .btn-quick-create:hover {
            background: var(--pu-navy-light); transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* ══════════════════════════════════════
           PAGE CONTENT AREA
        ══════════════════════════════════════ */
        .content { padding: 28px 32px 52px; flex: 1; }

        /* ══════════════════════════════════════
           ALERT TOAST — with auto-dismiss
        ══════════════════════════════════════ */
        .alert-toast {
            margin-bottom: 18px; padding: 12px 18px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: space-between;
            font-size: 13.5px; font-weight: 500;
            animation: fadeSlideIn 0.35s ease;
            box-shadow: var(--shadow-sm);
        }
        .alert-toast-content { display: flex; align-items: center; gap: 10px; }
        .alert-success {
            background: var(--success-bg); border: 1px solid #a7f3d0; color: #065f46;
        }
        .alert-danger {
            background: var(--danger-bg); border: 1px solid #fecaca; color: #b91c1c;
        }
        .alert-warning {
            background: var(--warning-bg); border: 1px solid #fde68a; color: #92400e;
        }
        .alert-close-btn {
            background: none; border: none; font-size: 15px; cursor: pointer;
            color: inherit; opacity: 0.5; padding: 2px 4px; border-radius: 4px;
            transition: opacity 0.15s;
        }
        .alert-close-btn:hover { opacity: 1; }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ══════════════════════════════════════
           SHARED CARD COMPONENTS (used in subpages)
        ══════════════════════════════════════ */
        .panel-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }
        .panel-head {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg-surface);
        }
        .panel-head h3 { font-size: 14px; font-weight: 700; color: var(--text-main); }
        .panel-body { padding: 18px 20px; }

        .table-modern {
            width: 100%; border-collapse: collapse; text-align: left;
        }
        .table-modern th {
            font-size: 11px; text-transform: uppercase; letter-spacing: 0.7px;
            color: var(--text-muted); font-weight: 700;
            padding: 10px 14px; border-bottom: 1px solid var(--border);
            background: var(--bg-subtle);
        }
        .table-modern td {
            padding: 12px 14px; font-size: 13px;
            border-bottom: 1px solid #f3f4f6; vertical-align: middle;
        }
        .table-modern tr:last-child td { border-bottom: none; }
        .table-modern tbody tr:hover td { background: var(--bg-subtle); }

        .badge-status {
            display: inline-block; padding: 2px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .badge-draft  { background: var(--pu-gold-light); color: #92400e; }
        .badge-final  { background: #dcfce7; color: #166534; }

        .section-title {
            font-size: 15px; font-weight: 700; color: var(--text-main);
            display: flex; align-items: center; gap: 8px;
        }
        .section-title::before {
            content: ''; width: 4px; height: 18px;
            background: var(--pu-gold); border-radius: 3px;
        }

        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); }
        .empty-state .empty-icon { font-size: 36px; display: block; margin-bottom: 10px; }
        .empty-state p { font-size: 13.5px; }

        /* ── Pagination Bar (konsisten untuk 1 maupun banyak halaman) ── */
        .pg-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .pg-info {
            font-size: 13px;
            color: var(--text-muted);
        }
        .pg-info strong {
            color: var(--text-main);
            font-weight: 700;
        }
        .pg-links {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .pg-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border);
            background: var(--bg-surface);
            color: var(--text-main);
            transition: all 0.15s;
            user-select: none;
        }
        a.pg-btn:hover {
            background: var(--pu-navy-50);
            border-color: var(--pu-navy);
            color: var(--pu-navy);
        }
        .pg-btn.is-active {
            background: var(--pu-navy);
            color: #ffffff;
            border-color: var(--pu-navy);
        }
        .pg-btn.is-disabled {
            opacity: 0.45;
            cursor: default;
            background: var(--bg-subtle);
            color: var(--text-muted);
        }
        .pg-dots {
            padding: 0 4px;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ── Bulk Selection & Pagination Utilities ── */
        .chk-input {
            width: 16px;
            height: 16px;
            accent-color: var(--pu-navy);
            cursor: pointer;
            vertical-align: middle;
        }
        tr.row-selected td {
            background: #eff6ff !important;
        }
        .bulk-bar {
            display: none;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .bulk-bar.is-visible {
            display: inline-flex;
        }
        .bulk-count-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(239, 68, 68, 0.10);
            color: var(--danger);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .btn-bulk-delete {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            background: var(--danger);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-bulk-delete:hover {
            background: #dc2626;
        }
        .btn-bulk-restore {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #a7f3d0;
            background: #059669;
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-bulk-restore:hover {
            background: #047857;
        }
        .btn-bulk-cancel {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-bulk-cancel:hover {
            background: var(--bg-soft);
            color: var(--text-main);
        }
        .pagination-wrap {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: #ffffff;
        }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar-backdrop.show { display: block; }
            .wrapper { margin-left: 0; }
            .topbar { display: flex; }
            .sidebar-toggle-btn, .sidebar-reopen-btn { display: none !important; }
            body.sidebar-hidden .dash-header,
            body.sidebar-hidden .subpage-header { padding-left: 0; }
            .content { padding: 18px 16px 40px; }
        }
        @media (max-width: 640px) {
            .page-breadcrumb p { display: none; }
            .topbar-user-info { display: none; }
            .btn-quick-create .btn-cta-text { display: none; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- BACKDROP (mobile) -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- FLOATING REOPEN SIDEBAR BUTTON (desktop when hidden) -->
    <button type="button" class="sidebar-reopen-btn" onclick="toggleDesktopSidebar()" title="Tampilkan Menu Sidebar">
        <span>☰</span>
        <span>Menu</span>
    </button>

    <!-- ══════ SIDEBAR ══════ -->
    <aside class="sidebar" id="mainSidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-left">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo_pu.png') }}" alt="Logo PU">
                </div>
                <div class="brand-text">
                    <h1>BWS Kalimantan I</h1>
                    <span>Kementerian Pekerjaan Umum</span>
                </div>
            </div>
            <button type="button" class="sidebar-toggle-btn" onclick="toggleDesktopSidebar()" title="Sembunyikan Sidebar">
                ◀
            </button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-menu">
            <div class="menu-category">Menu</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>

            <div class="menu-category">Administrasi Surat</div>
            <a href="{{ route('surat.undangan.create') }}" class="nav-item {{ request()->routeIs('surat.undangan.create') ? 'active' : '' }}">
                <span class="nav-icon">✉️</span>
                <span>Buat Surat Undangan</span>
            </a>
            <a href="{{ route('surat.suket.create') }}" class="nav-item {{ request()->routeIs('surat.suket.create') ? 'active' : '' }}">
                <span class="nav-icon">📝</span>
                <span>Suket Lupa Absen</span>
            </a>
            <a href="{{ route('surat.undangan.index') }}" class="nav-item {{ request()->routeIs('surat.undangan.index') || request()->routeIs('surat.undangan.show') ? 'active' : '' }}">
                <span class="nav-icon">📁</span>
                <span>Arsip Surat Undangan</span>
            </a>
            <a href="{{ route('surat.suket.index') }}" class="nav-item {{ request()->routeIs('surat.suket.index') || request()->routeIs('surat.suket.show') ? 'active' : '' }}">
                <span class="nav-icon">🗂️</span>
                <span>Arsip Suket Absen</span>
            </a>
            <a href="{{ route('pegawai.index') }}" class="nav-item {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <span>Database Pegawai</span>
            </a>
            @php
                try {
                    $trashTotalNav = \App\Models\SuratUndangan::onlyTrashed()->count()
                        + \App\Models\SuketAbsen::onlyTrashed()->count()
                        + \App\Models\Pegawai::onlyTrashed()->count();
                } catch (\Throwable $e) {
                    $trashTotalNav = 0;
                }
            @endphp
            <a href="{{ route('trash.index') }}" class="nav-item {{ request()->routeIs('trash.*') ? 'active' : '' }}">
                <span class="nav-icon">🕒</span>
                <span>Riwayat Penghapusan</span>
                @if($trashTotalNav > 0)
                    <span class="nav-badge" style="background:#ef4444; color:#fff;">{{ $trashTotalNav }}</span>
                @endif
            </a>

            <div class="menu-category">Pengaturan</div>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <span class="nav-icon">⚙️</span>
                <span>Pengaturan Instansi</span>
            </a>
            <a href="{{ route('profil.index') }}" class="nav-item {{ request()->routeIs('profil.*') ? 'active' : '' }}">
                <span class="nav-icon">👤</span>
                <span>Profil & Kata Sandi</span>
            </a>
        </nav>

        <!-- User Card (footer) -->
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ Str::limit(Auth::user()->name ?? 'Administrator', 18) }}</div>
                    <div class="user-role"><span class="online-dot"></span> Online</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar dari sistem?')">
                    @csrf
                    <button type="submit" class="btn-logout-mini" title="Keluar dari sistem">🚪</button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ══════ WRAPPER ══════ -->
    <div class="wrapper">

        <!-- TOPBAR (Mobile Only) -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-hamburger" onclick="toggleSidebar()" aria-label="Buka Menu">☰</button>
                <span class="mobile-brand-label">BWS Kalimantan I</span>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="content">
            @if(!request()->routeIs('dashboard'))
                <div class="subpage-header">
                    <h1>@yield('header_title', 'Portal Tata Usaha')</h1>
                    <p>@yield('header_subtitle', 'BWS Kalimantan I — Kementerian Pekerjaan Umum')</p>
                </div>
            @endif
            {{-- Flash Alerts --}}
            @if(session('success'))
                <div class="alert-toast alert-success" id="alertToastSuccess">
                    <div class="alert-toast-content">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="document.getElementById('alertToastSuccess').remove()">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-toast alert-danger" id="alertToastError">
                    <div class="alert-toast-content">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="document.getElementById('alertToastError').remove()">✕</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert-toast alert-warning" id="alertToastWarning">
                    <div class="alert-toast-content">
                        <span>⚠️</span>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" class="alert-close-btn" onclick="document.getElementById('alertToastWarning').remove()">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div><!-- /.wrapper -->

    <script>
        // ── Sidebar toggle (mobile)
        function toggleSidebar() {
            const sidebar  = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar?.classList.toggle('show');
            backdrop?.classList.toggle('show');
        }

        // ── Sidebar hide/show (desktop)
        function toggleDesktopSidebar() {
            const isHidden = document.body.classList.toggle('sidebar-hidden');
            try { localStorage.setItem('bws_sidebar_hidden', isHidden ? '1' : '0'); } catch(e) {}
        }

        // Restore desktop sidebar state
        try {
            if (window.innerWidth > 1024 && localStorage.getItem('bws_sidebar_hidden') === '1') {
                document.body.classList.add('sidebar-hidden');
            }
        } catch(e) {}

        // ── Shared Bulk Selection Helper ───────────────────────────────
        window.initBulkSelect = function (cfg) {
            const selectAll = document.getElementById(cfg.selectAllId);
            const bar = document.getElementById(cfg.barId);
            const countEl = document.getElementById(cfg.countId);
            const form = document.getElementById(cfg.formId);
            const inputsWrap = document.getElementById(cfg.inputsId);
            const cancelBtn = cfg.cancelId ? document.getElementById(cfg.cancelId) : null;
            if (!selectAll || !form || !inputsWrap) return;

            const getRows = () => Array.from(document.querySelectorAll(cfg.rowSelector));

            function sync() {
                const rows = getRows();
                const checked = rows.filter(cb => cb.checked);
                rows.forEach(cb => {
                    const tr = cb.closest('tr');
                    if (tr) tr.classList.toggle('row-selected', cb.checked);
                });
                if (bar) bar.classList.toggle('is-visible', checked.length > 0);
                if (countEl) countEl.textContent = checked.length + ' dipilih';
                selectAll.checked = rows.length > 0 && checked.length === rows.length;
                selectAll.indeterminate = checked.length > 0 && checked.length < rows.length;
            }

            selectAll.addEventListener('change', () => {
                getRows().forEach(cb => { cb.checked = selectAll.checked; });
                sync();
            });

            document.addEventListener('change', (e) => {
                if (e.target && e.target.matches(cfg.rowSelector)) sync();
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    selectAll.checked = false;
                    getRows().forEach(cb => { cb.checked = false; });
                    sync();
                });
            }

            form.addEventListener('submit', (e) => {
                const checked = getRows().filter(cb => cb.checked);
                if (checked.length === 0) {
                    e.preventDefault();
                    return;
                }
                const label = cfg.entityLabel || 'data';
                const customPrompt = e.submitter && e.submitter.dataset && e.submitter.dataset.confirm
                    ? e.submitter.dataset.confirm.replace('{count}', String(checked.length))
                    : (cfg.confirmText ? cfg.confirmText.replace('{count}', String(checked.length)) : 'Pindahkan ' + checked.length + ' ' + label + ' terpilih ke Riwayat Penghapusan? Data masih bisa dipulihkan.');
                if (!confirm(customPrompt)) {
                    e.preventDefault();
                    return;
                }
                inputsWrap.innerHTML = '';
                checked.forEach(cb => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'ids[]';
                    inp.value = cb.value;
                    inputsWrap.appendChild(inp);
                });
            });

            sync();
        };

        // ── Auto-dismiss alerts after 5 s
        document.addEventListener('DOMContentLoaded', () => {
            ['alertToastSuccess','alertToastError','alertToastWarning'].forEach(id => {
                const el = document.getElementById(id);
                if (el) setTimeout(() => el.remove(), 5000);
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
