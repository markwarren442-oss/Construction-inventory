<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') | ConstructLogix Bulalacao</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
    <style>
        :root {
            --fb-blue: #1877f2;
            --fb-blue-hover: #166fe5;
            --fb-bg: #f0f2f5;
            --fb-card: #ffffff;
            --fb-border: #dce1e7;
            --fb-text: #050505;
            --fb-muted: #65676b;
            --navbar-h: 56px;
            --sidebar-w: 260px;
            --sidebar-collapsed-w: 68px;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--fb-bg);
            color: var(--fb-text);
            margin: 0;
            padding-top: var(--navbar-h);
            font-size: 14.5px;
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── TOP NAVBAR ── */
        .fb-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--navbar-h);
            background: #ffffff;
            border-bottom: 1px solid var(--fb-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            z-index: 1040;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        .fb-nav-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        /* Hamburger Toggle Button */
        .fb-toggle-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #e4e6eb;
            border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            color: #050505;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s, color .15s;
        }
        .fb-toggle-btn:hover { background: #d8dadf; color: var(--fb-blue); }

        .fb-logo {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none;
            min-width: 0;
        }

        .fb-logo-icon {
            width: 36px; height: 36px;
            background: var(--fb-blue);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 17px;
            flex-shrink: 0;
        }

        .fb-logo-text {
            font-size: 16px; font-weight: 800;
            color: var(--fb-blue);
            line-height: 1.1; letter-spacing: -0.3px;
            white-space: nowrap;
        }

        .fb-logo-sub {
            font-size: 11px; color: var(--fb-muted); font-weight: 500;
            white-space: nowrap;
        }

        .fb-nav-right {
            display: flex; align-items: center; gap: 8px;
            flex-shrink: 0;
        }

        .fb-icon-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: #e4e6eb;
            border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; color: #050505;
            cursor: pointer; text-decoration: none;
            transition: background .15s;
            position: relative;
            flex-shrink: 0;
        }
        .fb-icon-btn:hover { background: #d8dadf; }

        .fb-user-pill {
            display: flex; align-items: center; gap: 8px;
            padding: 3px 10px 3px 4px;
            border-radius: 20px;
            background: #e4e6eb;
            cursor: pointer; border: none;
            font-size: 13.5px; font-weight: 600;
            transition: background .15s;
            max-width: 180px;
        }
        .fb-user-pill:hover { background: #d8dadf; }

        .fb-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--fb-blue);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px;
            flex-shrink: 0;
        }

        /* ── TOGGLEABLE SIDEBAR & LAYOUT (PURPLE CURVED INWARD THEME) ── */
        .app-layout {
            display: flex;
            min-height: calc(100vh - var(--navbar-h));
            position: relative;
            width: 100%;
        }

        .fb-sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: linear-gradient(180deg, #5b46b8 0%, #4d38a5 50%, #3d288f 100%);
            border-right: none;
            padding: 0 0 20px 0;
            position: fixed;
            top: var(--navbar-h); bottom: 0; left: 0;
            overflow-y: auto; overflow-x: hidden;
            scrollbar-width: none;
            transition: width .2s ease, transform .25s ease;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 16px rgba(45, 25, 100, 0.14);
        }

        .fb-sidebar::-webkit-scrollbar { display: none; }

        /* Sidebar Brand Header */
        .sidebar-brand-header {
            padding: 24px 20px 14px 24px;
            text-align: left;
        }

        .sidebar-brand-title {
            color: #ffffff;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-brand-sub {
            color: rgba(255, 255, 255, 0.65);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-top: 3px;
        }

        /* Sidebar Section Titles & Dividers */
        .sidebar-section-title {
            font-size: 10.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.52);
            padding: 12px 20px 4px 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.12);
            margin: 8px 16px;
        }

        /* Navigation List */
        .sidebar-nav-list {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 4px 0;
            flex-grow: 1;
        }

        /* Inactive Menu Item */
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px 11px 24px;
            margin: 1px 14px 1px 14px;
            border-radius: 12px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.88);
            font-weight: 500;
            font-size: 14px;
            transition: all .15s ease;
            cursor: pointer;
            border: none;
            background: transparent;
            width: auto;
            text-align: left;
            position: relative;
        }

        .sidebar-item:hover:not(.active) {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .sidebar-icon {
            font-size: 15px;
            width: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: rgba(255, 255, 255, 0.88);
            transition: color .15s ease;
        }

        .sidebar-label {
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-badge {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
            flex-shrink: 0;
            transition: background .15s ease;
        }

        .sidebar-badge-alert {
            background: #ff7675 !important;
            color: #ffffff !important;
        }

        /* ── ACTIVE CUTOUT INWARD TAB (SIGNATURE EFFECT) ── */
        .sidebar-item.active {
            background: var(--fb-bg);
            color: #4a34a4;
            font-weight: 700;
            border-radius: 30px 0 0 30px;
            margin: 4px 0 4px 14px;
            padding: 12px 16px 12px 20px;
            position: relative;
            box-shadow: none;
        }

        .sidebar-item.active .sidebar-icon {
            color: #4a34a4;
        }

        .sidebar-item.active .sidebar-badge {
            background: #4a34a4;
            color: #ffffff;
        }

        /* Concave curved cutouts on top & bottom right of active tab */
        .sidebar-item.active::before {
            content: '';
            position: absolute;
            top: -20px;
            right: 0;
            width: 20px;
            height: 20px;
            background: transparent;
            border-bottom-right-radius: 20px;
            box-shadow: 7px 7px 0 7px var(--fb-bg);
            pointer-events: none;
            z-index: 5;
        }

        .sidebar-item.active::after {
            content: '';
            position: absolute;
            bottom: -20px;
            right: 0;
            width: 20px;
            height: 20px;
            background: transparent;
            border-top-right-radius: 20px;
            box-shadow: 7px -7px 0 7px var(--fb-bg);
            pointer-events: none;
            z-index: 5;
        }

        /* Bottom Origami Mascot Illustration */
        .sidebar-mascot-wrap {
            margin-top: auto;
            padding: 16px 20px 8px 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Main Content wrapper */
        .fb-content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-w);
            padding: 20px;
            min-width: 0;
            max-width: 100%;
            transition: margin-left .2s ease;
        }

        .fb-content-container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Desktop Collapsed state */
        @media (min-width: 992px) {
            body.sidebar-collapsed .fb-sidebar {
                width: var(--sidebar-collapsed-w);
            }
            body.sidebar-collapsed .fb-content-wrapper {
                margin-left: var(--sidebar-collapsed-w);
            }
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .sidebar-section-title,
            body.sidebar-collapsed .sidebar-badge,
            body.sidebar-collapsed .sidebar-brand-header,
            body.sidebar-collapsed .sidebar-mascot-wrap {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-item {
                justify-content: center;
                padding: 12px 0;
                margin: 2px 8px;
                border-radius: 12px;
            }
            body.sidebar-collapsed .sidebar-item.active {
                border-radius: 12px;
                margin: 2px 8px;
                padding: 12px 0;
            }
            body.sidebar-collapsed .sidebar-item.active::before,
            body.sidebar-collapsed .sidebar-item.active::after {
                display: none;
            }
        }

        /* Mobile / Tablet (<992px): Slide-out drawer */
        .sidebar-backdrop {
            position: fixed;
            top: var(--navbar-h); left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,.45);
            z-index: 1025; display: none;
            backdrop-filter: blur(2px);
        }

        @media (max-width: 991.98px) {
            .fb-sidebar {
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0,0,0,.3);
            }
            body.sidebar-mobile-open .fb-sidebar { transform: translateX(0); }
            body.sidebar-mobile-open .sidebar-backdrop { display: block; }
            .fb-content-wrapper { margin-left: 0 !important; padding: 14px 12px; }
        }

        @media (max-width: 576px) {
            .fb-navbar { padding: 0 10px; }
            .fb-content-wrapper { padding: 12px 8px; }
        }

        /* ── CLEAN CARDS ── */
        .fb-card {
            background: var(--fb-card);
            border-radius: 10px;
            border: 1px solid var(--fb-border);
            padding: 16px; margin-bottom: 14px;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }

        @media (max-width: 576px) {
            .fb-card { padding: 12px; }
        }

        /* ── BUTTONS ── */
        .btn-fb-add    { background: #42b72a; border: none; color: #fff; font-weight: 600; border-radius: 8px; padding: 8px 14px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-add:hover { background: #36a420; color: #fff; }

        .btn-fb-view   { background: #1877f2; border: none; color: #fff; font-weight: 600; border-radius: 8px; padding: 8px 14px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-view:hover { background: #166fe5; color: #fff; }

        .btn-fb-edit   { background: #e4e6eb; border: none; color: #050505; font-weight: 600; border-radius: 8px; padding: 8px 12px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-edit:hover { background: #d8dadf; }

        .btn-fb-action { background: #ff7043; border: none; color: #fff; font-weight: 600; border-radius: 8px; padding: 8px 14px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-action:hover { background: #f4511e; color: #fff; }

        .btn-fb-warn   { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; font-weight: 600; border-radius: 8px; padding: 8px 12px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-warn:hover { background: #ffe8a1; color: #856404; }

        .btn-fb-danger { background: #fce8e8; border: none; color: #c0392b; font-weight: 600; border-radius: 8px; padding: 8px 12px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-danger:hover { background: #f9d5d5; color: #a93226; }

        .btn-fb-scan   { background: #7b4ff7; border: none; color: #fff; font-weight: 600; border-radius: 8px; padding: 8px 14px; font-size: 13.5px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-fb-scan:hover { background: #6839e5; color: #fff; }

        /* ── STAT CARDS ── */
        .stat-block {
            background: #fff; border-radius: 10px;
            border: 1px solid var(--fb-border);
            padding: 14px 16px;
            display: flex; align-items: center; gap: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
            height: 100%;
        }

        .stat-icon-wrap {
            width: 46px; height: 46px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }

        .stat-value { font-size: 22px; font-weight: 800; line-height: 1.1; word-break: break-word; }
        .stat-label { font-size: 12.5px; color: var(--fb-muted); margin-top: 2px; font-weight: 500; }

        /* ── BADGES & TX AVATARS ── */
        .badge-available   { background: #e6f9f1; color: #219150; border: 1px solid #b7e8cd; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-low         { background: #fff8e1; color: #c77700; border: 1px solid #ffe082; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-out         { background: #fdecea; color: #c62828; border: 1px solid #f9bebe; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-received    { background: #e6f9f1; color: #219150; border: 1px solid #b7e8cd; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-issued      { background: #e7f3ff; color: #1877f2; border: 1px solid #b3d4f9; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-transferred { background: #f3e8ff; color: #7b4ff7; border: 1px solid #d9b8fb; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-damaged     { background: #fdecea; color: #c62828; border: 1px solid #f9bebe; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }
        .badge-used        { background: #f0f2f5; color: #444950; border: 1px solid #dce1e7; font-weight: 700; border-radius: 20px; padding: 3px 9px; font-size: 11.5px; display: inline-block; white-space: nowrap; }

        .tx-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .tx-avatar-received    { background: #e6f9f1; color: #219150; }
        .tx-avatar-issued      { background: #e7f3ff; color: #1877f2; }
        .tx-avatar-transferred { background: #f3e8ff; color: #7b4ff7; }
        .tx-avatar-damaged,
        .tx-avatar-lost        { background: #fdecea; color: #c62828; }
        .tx-avatar-used,
        .tx-avatar-default     { background: #f0f2f5; color: #65676b; }

        /* Notification Dot */
        .notif-dot {
            position: absolute; top: 3px; right: 3px;
            width: 9px; height: 9px;
            background: #e41e3f; border-radius: 50%;
            border: 2px solid #fff;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex; align-items: center;
            justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
            margin-bottom: 16px;
        }

        .page-header-title { font-size: 20px; font-weight: 800; margin: 0; color: #050505; }
        .page-header-sub   { font-size: 13.5px; color: var(--fb-muted); margin: 2px 0 0; }

        @media (max-width: 576px) {
            .page-header { flex-direction: column; align-items: flex-start; gap: 8px; }
            .page-header > .d-flex { width: 100%; }
            .page-header > .d-flex > .btn { flex: 1; }
        }

        /* ── FORMS & INPUTS ── */
        .form-control, .form-select {
            border-radius: 8px; border-color: var(--fb-border);
            font-size: 14px; padding: 9px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--fb-blue);
            box-shadow: 0 0 0 3px rgba(24,119,242,.15);
        }
        .form-label { font-weight: 600; font-size: 13px; margin-bottom: 5px; color: #1c1e21; }

        .fb-alert-success {
            background: #e6f9f1; color: #219150; border: 1.5px solid #b7e8cd;
            border-radius: 10px; padding: 12px 16px; font-weight: 600; font-size: 13.5px;
            display: flex; align-items: center; gap: 10px;
        }
        .fb-alert-error {
            background: #fdecea; color: #c62828; border: 1.5px solid #f9bebe;
            border-radius: 10px; padding: 12px 16px; font-weight: 600; font-size: 13.5px;
            display: flex; align-items: center; gap: 10px;
        }

        /* ── RESPONSIVE TABLES ── */
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table {
            vertical-align: middle;
            margin-bottom: 0;
        }
        .table th {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--fb-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            background: #f8f9fa;
        }
        .table td {
            font-size: 13.5px;
            color: var(--fb-text);
        }

        /* ── POLISHED MODALS ── */
        .modal-content {
            border-radius: 14px !important;
            border: none !important;
            box-shadow: 0 12px 40px rgba(0,0,0,.15) !important;
            overflow: hidden;
        }

        .modal-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid var(--fb-border) !important;
            background: #fff;
        }

        .modal-header .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #050505;
        }

        .modal-body {
            padding: 18px 20px;
            max-height: 78vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--fb-border) !important;
            background: #f8f9fb;
            gap: 8px;
        }

        /* Modal icon header block */
        .modal-icon-header {
            text-align: center;
            padding: 20px 20px 10px;
        }

        .modal-icon-circle {
            width: 54px; height: 54px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .modal-icon-title {
            font-size: 17px; font-weight: 800; color: #050505;
            margin: 0 0 4px;
        }

        .modal-icon-sub {
            font-size: 13px; color: var(--fb-muted);
            margin: 0;
        }

        /* Input group icon prefix */
        .input-icon-group { position: relative; }
        .input-icon-group .form-control,
        .input-icon-group .form-select { padding-left: 38px; }
        .input-icon-group .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: var(--fb-muted); font-size: 13.5px; pointer-events: none;
            z-index: 1;
        }

        /* ── PRINT STYLES ── */
        @media print {
            body {
                padding-top: 0 !important;
                background: #fff !important;
                color: #000 !important;
            }
            .fb-navbar, .fb-sidebar, .sidebar-backdrop, .btn, .page-header button, .dropdown {
                display: none !important;
            }
            .fb-content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            .fb-card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════
     TOP NAVBAR WITH TOGGLE BUTTON
══════════════════════════════════════ -->
<nav class="fb-navbar">

    <!-- Left: Hamburger Toggle + Logo -->
    <div class="fb-nav-left">
        <button type="button" class="fb-toggle-btn" onclick="toggleSidebar(event)" title="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a href="{{ route('dashboard') }}" class="fb-logo">
            <div class="fb-logo-icon">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div>
                <div class="fb-logo-text">ConstructLogix</div>
                <div class="fb-logo-sub d-none d-sm-block">Bulalacao, Oriental Mindoro</div>
            </div>
        </a>
    </div>

    <!-- Right: Quick Scan + Notifications + Profile -->
    <div class="fb-nav-right">

        <!-- Quick Scan Button -->
        <a href="{{ route('qr.scanner') }}" class="btn btn-fb-scan btn-sm d-none d-sm-flex align-items-center gap-2" title="Scan QR Code">
            <i class="fa-solid fa-camera"></i>
            <span>Scan QR</span>
        </a>

        <!-- Notifications Dropdown -->
        <div class="dropdown">
            @php
                $lowStockCount = \App\Models\Material::whereColumn('current_stock', '<=', 'minimum_stock_level')->count();
                $outOfStockCount = \App\Models\Material::where('current_stock', '<=', 0)->count();
            @endphp
            <button class="fb-icon-btn" type="button" data-bs-toggle="dropdown" title="Alerts">
                <i class="fa-solid fa-bell"></i>
                @if($lowStockCount > 0)
                    <span class="notif-dot"></span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow p-2" style="min-width: 300px; border-radius: 12px; border-color: var(--fb-border);">
                <li class="px-2 py-1 fw-bold text-dark" style="font-size: 14px;">
                    <i class="fa-solid fa-bell text-warning me-2"></i> System Alerts
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                @if($lowStockCount > 0)
                <li>
                    <a class="dropdown-item rounded-3 py-2 d-flex gap-2 align-items-start" href="{{ route('inventory.low-stock') }}" style="font-size: 13px;">
                        <div class="bg-warning-subtle rounded-circle p-2 flex-shrink-0"><i class="fa-solid fa-triangle-exclamation text-warning"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Low Stock Alert</div>
                            <div class="text-muted" style="font-size: 12px;">{{ $lowStockCount }} material{{ $lowStockCount > 1 ? 's are' : ' is' }} running low on stock</div>
                        </div>
                    </a>
                </li>
                @endif
                @if($outOfStockCount > 0)
                <li>
                    <a class="dropdown-item rounded-3 py-2 d-flex gap-2 align-items-start" href="{{ route('inventory.index') }}?status=out_of_stock" style="font-size: 13px;">
                        <div class="bg-danger-subtle rounded-circle p-2 flex-shrink-0"><i class="fa-solid fa-circle-xmark text-danger"></i></div>
                        <div>
                            <div class="fw-bold text-dark">Out of Stock</div>
                            <div class="text-muted" style="font-size: 12px;">{{ $outOfStockCount }} material{{ $outOfStockCount > 1 ? 's have' : ' has' }} zero stock remaining</div>
                        </div>
                    </a>
                </li>
                @endif
                @if($lowStockCount === 0 && $outOfStockCount === 0)
                <li>
                    <div class="px-3 py-3 text-center">
                        <i class="fa-solid fa-circle-check text-success fa-lg mb-2"></i>
                        <div class="fw-bold text-dark" style="font-size: 13px;">All Clear</div>
                        <div class="text-muted" style="font-size: 12px;">No stock alerts at this time</div>
                    </div>
                </li>
                @endif
                <li><hr class="dropdown-divider my-1"></li>
                <li class="text-center">
                    <a class="dropdown-item text-center fw-bold text-primary rounded-3" href="{{ route('inventory.low-stock') }}" style="font-size: 13px;">View Inventory Alerts</a>
                </li>
            </ul>
        </div>

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="fb-user-pill" type="button" data-bs-toggle="dropdown">
                <div class="fb-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                <span class="d-none d-md-inline text-dark" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Auth::user()->name }}</span>
                <i class="fa-solid fa-chevron-down" style="font-size: 11px; color: var(--fb-muted);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow p-2" style="min-width: 220px; border-radius: 12px; border-color: var(--fb-border);">
                <li class="px-2 py-2 border-bottom mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="fb-avatar" style="width: 36px; height: 36px; font-size: 14px;">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
                        <div style="min-width: 0;">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 14px;">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 12px;">{{ Auth::user()->role->name ?? 'User' }}</div>
                        </div>
                    </div>
                </li>
                <li><a class="dropdown-item rounded-3 py-2" href="{{ route('profile.index') }}" style="font-size: 13px;"><i class="fa-solid fa-user me-2 text-muted"></i> My Profile</a></li>
                <li><a class="dropdown-item rounded-3 py-2" href="{{ route('dashboard') }}" style="font-size: 13px;"><i class="fa-solid fa-house me-2 text-muted"></i> Home</a></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><button type="button" class="dropdown-item rounded-3 py-2 text-danger fw-semibold" onclick="openLogoutModal()" style="font-size: 13px;"><i class="fa-solid fa-right-from-bracket me-2"></i> Log Out</button></li>
            </ul>
        </div>

    </div>
</nav>

<!-- ══════════════════════════════════════
     APP LAYOUT: TOGGLEABLE SIDEBAR + CONTENT
══════════════════════════════════════ -->
<div class="app-layout">

    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" onclick="closeSidebarMobile()"></div>

    <!-- TOGGLEABLE SIDEBAR -->
    <aside class="fb-sidebar" id="appSidebar">
        
        <!-- Header Branding (matching reference image) -->
        <div class="sidebar-brand-header">
            <div class="sidebar-brand-title">
                <i class="fa-solid fa-cubes-stacked" style="color: #67e8f9; font-size: 15px;"></i>
                SUCCESS
            </div>
            <div class="sidebar-brand-sub">CONSTRUCTLOGIX &bull; LOGISTICS</div>
        </div>

        <div class="sidebar-nav-list">
            <!-- Profile Tab (at the top, matching reference screenshot) -->
            <a href="{{ route('profile.index') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="My Profile">
                <div class="sidebar-icon">
                    <i class="fa-regular fa-user"></i>
                </div>
                <span class="sidebar-label">Profile</span>
            </a>

            <!-- Home / Dashboard -->
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Home / Dashboard">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-house"></i>
                </div>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-section-title">Materials & QR</div>

            @php
                $sidebarMaterialCount = \App\Models\Material::count();
                $sidebarQrCount = \App\Models\QrCode::count();
                $sidebarMovementCount = \App\Models\Transaction::whereDate('created_at', today())->count();
                $sidebarDamagedCount = \App\Models\Transaction::whereIn('type', ['damaged', 'lost'])->count();
                $sidebarLocationCount = \App\Models\Location::count();
                $sidebarUserCount = \App\Models\User::count();
            @endphp

            <!-- Materials Catalog -->
            <a href="{{ route('materials.index') }}" class="sidebar-item {{ request()->routeIs('materials.*') ? 'active' : '' }}" title="Materials Catalog">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <span class="sidebar-label">Materials</span>
                <span class="sidebar-badge">{{ $sidebarMaterialCount }}</span>
            </a>

            <!-- QR Scanner -->
            <a href="{{ route('qr.scanner') }}" class="sidebar-item {{ request()->routeIs('qr.scanner') ? 'active' : '' }}" title="Scan QR Code">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <span class="sidebar-label">Scan QR</span>
            </a>

            <!-- QR Code Ledger -->
            <a href="{{ route('qr.index') }}" class="sidebar-item {{ request()->routeIs('qr.index') ? 'active' : '' }}" title="QR Codes List">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <span class="sidebar-label">QR Codes</span>
                <span class="sidebar-badge">{{ $sidebarQrCount }}</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-section-title">Stock & Movements</div>

            <!-- Material Movements -->
            <a href="{{ route('transactions.index') }}" class="sidebar-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}" title="Material Movements">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </div>
                <span class="sidebar-label">Movements</span>
                <span class="sidebar-badge">{{ $sidebarMovementCount }}</span>
            </a>

            <!-- Inventory Balances -->
            <a href="{{ route('inventory.index') }}" class="sidebar-item {{ request()->routeIs('inventory.index') ? 'active' : '' }}" title="Inventory Stock">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <span class="sidebar-label">Inventory Stock</span>
            </a>

            <!-- Low Stock Alerts -->
            <a href="{{ route('inventory.low-stock') }}" class="sidebar-item {{ request()->routeIs('inventory.low-stock') ? 'active' : '' }}" title="Low Stock Alerts">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <span class="sidebar-label">Low Stock Alerts</span>
                <span class="sidebar-badge {{ $lowStockCount > 0 ? 'sidebar-badge-alert' : '' }}">{{ $lowStockCount }}</span>
            </a>

            <!-- Damaged / Lost -->
            <a href="{{ route('inventory.damaged-lost') }}" class="sidebar-item {{ request()->routeIs('inventory.damaged-lost') ? 'active' : '' }}" title="Damaged / Lost">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-file-circle-xmark"></i>
                </div>
                <span class="sidebar-label">Damaged / Lost</span>
                @if($sidebarDamagedCount > 0)
                    <span class="sidebar-badge sidebar-badge-alert">{{ $sidebarDamagedCount }}</span>
                @endif
            </a>

            <!-- Depots & Sites -->
            <a href="{{ route('locations.index') }}" class="sidebar-item {{ request()->routeIs('locations.*') ? 'active' : '' }}" title="Depots & Sites">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <span class="sidebar-label">Depots &amp; Sites</span>
                <span class="sidebar-badge">{{ $sidebarLocationCount }}</span>
            </a>

            @if(!auth()->user()->hasRole('site-personnel'))
            <!-- Reports & Analytics -->
            <a href="{{ route('reports.index') }}" class="sidebar-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" title="Reports & Analytics">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-chart-bar"></i>
                </div>
                <span class="sidebar-label">Reports</span>
            </a>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="sidebar-divider"></div>
            <div class="sidebar-section-title">Administration</div>

            <!-- Manage Users -->
            <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Manage Users">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span class="sidebar-label">Users</span>
                <span class="sidebar-badge">{{ $sidebarUserCount }}</span>
            </a>

            <!-- Activity Logs -->
            <a href="{{ route('activity-logs.index') }}" class="sidebar-item {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" title="Activity Logs">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <span class="sidebar-label">Activity Logs</span>
            </a>
            @endif

            <div class="sidebar-divider"></div>

            <!-- Logout Button -->
            <button type="button" class="sidebar-item" onclick="openLogoutModal()" title="Log Out" style="color: rgba(255, 255, 255, 0.75);">
                <div class="sidebar-icon">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <span class="sidebar-label">Log Out</span>
            </button>
        </div>

        <!-- ══ Bottom Origami Hummingbird Mascot (as in reference image) ══ -->
        <div class="sidebar-mascot-wrap">
            <svg width="125" height="125" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: drop-shadow(0 8px 16px rgba(0,0,0,0.3)); transform: rotate(-5deg);">
                <!-- Wing Feathers Layer 1 (Mint / Emerald) -->
                <path d="M72 75 L30 45 L50 78 Z" fill="#55efc4" opacity="0.95"/>
                <path d="M74 78 L22 56 L45 84 Z" fill="#00cec9"/>
                <path d="M75 82 L20 68 L42 90 Z" fill="#00b894"/>
                <path d="M76 86 L24 80 L44 96 Z" fill="#10ac84"/>
                <path d="M78 90 L30 92 L50 102 Z" fill="#1dd1a1"/>
                <path d="M80 94 L40 104 L58 108 Z" fill="#2ed573"/>

                <!-- Wing Feathers Layer 2 (Cyan / Azure) -->
                <path d="M75 75 L52 35 L70 65 Z" fill="#81ecec"/>
                <path d="M77 78 L42 48 L65 72 Z" fill="#74b9ff"/>
                <path d="M80 82 L38 60 L62 80 Z" fill="#0984e3"/>
                <path d="M82 86 L44 72 L66 88 Z" fill="#0abde3"/>

                <!-- Hummingbird Torso / Core (Deep Cyan & Indigo) -->
                <polygon points="70,65 92,60 88,85 75,82" fill="#00cec9"/>
                <polygon points="88,85 106,78 98,105 82,98" fill="#4834d4"/>
                <polygon points="98,105 110,95 104,118 90,114" fill="#6c5ce7"/>

                <!-- Bird Head & Beak (Magenta & Violet) -->
                <polygon points="92,60 114,54 108,70 88,72" fill="#a29bfe"/>
                <polygon points="114,54 126,58 116,68 108,70" fill="#e056fd"/>
                <polygon points="126,58 148,64 128,68" fill="#be2edd"/> <!-- Beak tip -->

                <!-- Bird Throat & Chest (Vibrant Pink / Fuchsia) -->
                <polygon points="108,70 122,70 112,86 98,82" fill="#ff7675"/>
                <polygon points="112,86 126,84 116,98 104,95" fill="#fd79a8"/>
                <polygon points="116,98 128,96 118,110 106,106" fill="#e84393"/>

                <!-- Tail Feathers (Gradient Warm Sunset Gold & Pink) -->
                <polygon points="90,114 78,142 88,146 96,122" fill="#ff7675"/>
                <polygon points="96,122 88,146 98,150 102,126" fill="#fab1a0"/>
                <polygon points="102,126 98,150 108,152 106,128" fill="#ffeaa7"/>
                <polygon points="106,128 108,152 118,150 110,126" fill="#fdcb6e"/>
                <polygon points="110,126 118,150 126,144 114,122" fill="#e17055"/>
            </svg>
        </div>

    </aside>

    <!-- ══ MAIN CONTENT AREA ══ -->
    <main class="fb-content-wrapper">
        <div class="fb-content-container">

            @if (session('success'))
                <div class="fb-alert-success mb-3">
                    <i class="fa-solid fa-circle-check fa-lg"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="fb-alert-error mb-3">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')

            <div style="height: 30px;"></div>
        </div>
    </main>

</div>

<!-- ══════════════════════════════════════
     LOGOUT MODAL (Polished)
══════════════════════════════════════ -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-icon-header">
                <div class="modal-icon-circle" style="background: #fdecea; color: #c62828;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
                <h5 class="modal-icon-title">Sign Out</h5>
                <p class="modal-icon-sub">Are you sure you want to log out of your session?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-fb-edit w-50" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark me-1"></i> Cancel
                </button>
                <form method="POST" action="{{ route('logout') }}" class="w-50">
                    @csrf
                    <button type="submit" class="btn btn-fb-danger w-100">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar(e) {
        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }
        
        const currentScrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

        if (window.innerWidth <= 991.98) {
            document.body.classList.toggle('sidebar-mobile-open');
        } else {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', document.body.classList.contains('sidebar-collapsed'));
        }

        window.scrollTo(0, currentScrollY);
        requestAnimationFrame(() => {
            window.scrollTo(0, currentScrollY);
        });
    }

    function closeSidebarMobile() {
        document.body.classList.remove('sidebar-mobile-open');
    }

    function openLogoutModal() {
        new bootstrap.Modal(document.getElementById('logoutModal')).show();
    }

    // Restore desktop collapsed state
    document.addEventListener('DOMContentLoaded', () => {
        if (window.innerWidth > 991.98 && localStorage.getItem('sidebar_collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }

        const sidebar = document.getElementById('appSidebar');
        if (sidebar) {
            const savedScroll = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedScroll) {
                sidebar.scrollTop = parseInt(savedScroll, 10);
            }
            sidebar.addEventListener('scroll', () => {
                sessionStorage.setItem('sidebar_scroll_pos', sidebar.scrollTop);
            }, { passive: true });
        }
    });
</script>
@stack('scripts')
</body>
</html>
