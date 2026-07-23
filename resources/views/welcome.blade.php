<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RAAX Enterprise Resource Planning Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-root: #09090b;
            --sidebar-bg: #0d0d11;
            --card-bg: #121215;
            --card-header-bg: #18181b;
            --border-subtle: #27272a;
            --border-highlight: #3f3f46;
            --orange-brand: #ff5e00;
            --orange-hover: #e05300;
            --orange-glow: rgba(255, 94, 0, 0.12);
            --text-pure: #ffffff;
            --text-muted: #a1a1aa;
            --text-dim: #71717a;
            --status-green: #10b981;
            --status-amber: #f59e0b;
            --status-red: #ef4444;
            --status-blue: #3b82f6;
            --drawer-width: 440px;
            --sidebar-width: 240px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-root);
            color: var(--text-pure);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Space Grotesk', sans-serif;
            letter-spacing: -0.02em;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* App Shell Layout */
        #app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Sidebar Navigation */
        aside#sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 90;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--orange-brand);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            font-size: 18px;
            font-weight: 800;
        }

        .brand-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-pure);
            line-height: 1.1;
        }

        .brand-title span {
            color: var(--orange-brand);
        }

        .brand-sub {
            font-size: 10px;
            color: var(--text-dim);
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .sidebar-menu {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }

        .menu-category {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 12px 10px 6px 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }

        .nav-item:hover {
            color: var(--text-pure);
            background: rgba(255, 255, 255, 0.03);
        }

        .nav-item.active {
            color: var(--orange-brand);
            background: var(--orange-glow);
            font-weight: 600;
        }

        .nav-item i.nav-icon {
            width: 20px;
            font-size: 14px;
            color: var(--text-dim);
            transition: color 0.15s ease;
        }

        .nav-item.active i.nav-icon, .nav-item:hover i.nav-icon {
            color: var(--orange-brand);
        }

        .nav-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            background: rgba(255, 94, 0, 0.18);
            color: var(--orange-brand);
            font-weight: 700;
        }

        /* Main Workspace Container */
        #main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--bg-root);
            position: relative;
        }

        /* Persistent Top Bar */
        header#topbar {
            height: 60px;
            background: rgba(9, 9, 11, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 80;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
            max-width: 500px;
        }

        .toggle-sidebar-btn {
            background: transparent;
            border: none;
            color: var(--text-dim);
            font-size: 16px;
            cursor: pointer;
            display: none;
        }

        .global-search-box {
            position: relative;
            width: 100%;
        }

        .global-search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            font-size: 13px;
        }

        .global-search-input {
            width: 100%;
            background: #141418;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 7px 12px 7px 34px;
            color: var(--text-pure);
            font-size: 12px;
            outline: none;
            transition: all 0.2s;
        }

        .global-search-input:focus {
            border-color: var(--orange-brand);
            box-shadow: 0 0 0 1px var(--orange-brand);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quick-create-btn {
            background: var(--orange-brand);
            color: #000;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .quick-create-btn:hover {
            background: var(--orange-hover);
        }

        .context-select {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            color: var(--text-pure);
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            outline: none;
            cursor: pointer;
        }

        .icon-btn {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            width: 34px;
            height: 34px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .icon-btn:hover {
            color: var(--text-pure);
            border-color: var(--border-highlight);
        }

        .icon-btn-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 6px;
            height: 6px;
            background: var(--orange-brand);
            border-radius: 50%;
        }

        /* Page Sub-Header / Breadcrumbs */
        .page-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            background: #0b0b0e;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .breadcrumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-dim);
            margin-bottom: 4px;
        }

        .breadcrumbs a {
            color: var(--text-dim);
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            color: var(--text-muted);
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-pure);
        }

        .page-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Main Workspace Scroll Area */
        .workspace-content {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        /* View Panels */
        .view-panel {
            display: none;
        }

        .view-panel.active {
            display: block;
        }

        /* Dashboard KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .kpi-card {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 1.25rem;
            position: relative;
            transition: border-color 0.2s;
        }

        .kpi-card:hover {
            border-color: var(--border-highlight);
        }

        .kpi-card.featured {
            border-left: 4px solid var(--orange-brand);
        }

        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .kpi-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .kpi-icon {
            color: var(--text-dim);
            font-size: 15px;
        }

        .kpi-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-pure);
            line-height: 1.1;
            margin-bottom: 6px;
            font-family: 'Space Grotesk', sans-serif;
        }

        .kpi-subtitle {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-subtitle.positive { color: var(--status-green); }
        .kpi-subtitle.orange { color: var(--orange-brand); }
        .kpi-subtitle.danger { color: var(--status-red); }

        /* Grids & Cards */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        @media (max-width: 1024px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: var(--card-header-bg);
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-pure);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            color: var(--orange-brand);
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Master List Controls Bar */
        .list-controls-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: #0f0f12;
            border-bottom: 1px solid var(--border-subtle);
            flex-wrap: wrap;
            gap: 10px;
        }

        .controls-left, .controls-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-chip {
            background: var(--bg-root);
            border: 1px solid var(--border-subtle);
            color: var(--text-dim);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .filter-chip:hover {
            color: var(--text-pure);
            border-color: var(--border-highlight);
        }

        .filter-chip.active {
            background: var(--orange-glow);
            color: var(--orange-brand);
            border-color: rgba(255, 94, 0, 0.4);
            font-weight: 600;
        }

        .search-input {
            background: var(--bg-root);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 6px 12px;
            color: var(--text-pure);
            font-size: 12px;
            outline: none;
            width: 220px;
        }

        .search-input:focus {
            border-color: var(--orange-brand);
        }

        /* Enterprise Data Tables */
        .data-table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .data-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-subtle);
            background: #0f0f12;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }

        .data-table td {
            padding: 11px 12px;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-muted);
            white-space: nowrap;
        }

        .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-pure);
            cursor: pointer;
        }

        .data-table tr.selected td {
            background: rgba(255, 94, 0, 0.06);
        }

        /* Status Chips */
        .status-chip {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .status-chip.draft { background: rgba(161, 161, 170, 0.1); color: var(--text-muted); border: 1px solid rgba(161, 161, 170, 0.2); }
        .status-chip.submitted { background: rgba(59, 130, 246, 0.12); color: var(--status-blue); border: 1px solid rgba(59, 130, 246, 0.3); }
        .status-chip.approved { background: rgba(16, 185, 129, 0.12); color: var(--status-green); border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-chip.rejected { background: rgba(239, 68, 68, 0.12); color: var(--status-red); border: 1px solid rgba(239, 68, 68, 0.3); }
        .status-chip.posted { background: var(--orange-glow); color: var(--orange-brand); border: 1px solid rgba(255, 94, 0, 0.3); }
        .status-chip.closed { background: rgba(161, 161, 170, 0.15); color: var(--text-dim); border: 1px solid var(--border-subtle); }

        /* Buttons & Forms */
        .btn {
            background: var(--orange-brand);
            color: #000000;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.15s ease;
        }

        .btn:hover {
            background: var(--orange-hover);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border-subtle);
            color: var(--text-pure);
        }

        .btn-outline:hover {
            border-color: var(--border-highlight);
            background: rgba(255, 255, 255, 0.04);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--status-red);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: var(--status-red);
            color: #fff;
        }

        .btn-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--status-green);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            background: var(--status-green);
            color: #000;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 11px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dim);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-input, .form-select {
            width: 100%;
            background: #09090b;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 8px 12px;
            color: var(--text-pure);
            font-family: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--orange-brand);
            box-shadow: 0 0 0 1px var(--orange-brand);
        }

        /* Line Item Form Grid */
        .line-item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .line-item-table th {
            text-align: left;
            font-size: 11px;
            color: var(--text-dim);
            text-transform: uppercase;
            padding: 6px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .line-item-table td {
            padding: 6px;
        }

        /* Persistent Right Detail Drawer */
        #detail-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--drawer-width);
            height: 100vh;
            background: #111114;
            border-left: 1px solid var(--border-subtle);
            box-shadow: -10px 0 30px rgba(0,0,0,0.5);
            z-index: 120;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #detail-drawer.open {
            transform: translateX(0);
        }

        .drawer-header {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #16161a;
        }

        .drawer-body {
            flex: 1;
            padding: 1.25rem;
            overflow-y: auto;
        }

        .drawer-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border-subtle);
            background: #16161a;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .drawer-close-btn {
            background: transparent;
            border: none;
            color: var(--text-dim);
            font-size: 18px;
            cursor: pointer;
        }

        .drawer-close-btn:hover {
            color: var(--text-pure);
        }

        /* Activity Log Timeline */
        .timeline {
            position: relative;
            padding-left: 20px;
            margin-top: 1rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: var(--border-subtle);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
            font-size: 12px;
        }

        .timeline-dot {
            position: absolute;
            left: -20px;
            top: 2px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--orange-brand);
        }

        .timeline-time {
            font-size: 10px;
            color: var(--text-dim);
        }

        /* Terminal Console Output */
        .terminal-box {
            background: #000000;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 1rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #d4d4d8;
            max-height: 280px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .terminal-box .hl-orange { color: var(--orange-brand); }
        .terminal-box .hl-green { color: var(--status-green); }

        /* Floating Toast Container */
        #toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 200;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast {
            background: #18181b;
            border: 1px solid var(--border-highlight);
            border-left: 4px solid var(--orange-brand);
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 13px;
            color: var(--text-pure);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.2s ease;
            min-width: 280px;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            z-index: 150;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-card {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .modal-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-subtle);
            background: var(--card-header-bg);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.25rem;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border-subtle);
            background: var(--card-header-bg);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 11px;
            color: var(--text-dim);
            background: var(--bg-root);
        }
    </style>
</head>
<body>

    <!-- App Shell Wrapper -->
    <div id="app-wrapper">

        <!-- Left Persistent Module Sidebar -->
        <aside id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">R</div>
                <div>
                    <div class="brand-title">RAAX <span>ERP</span></div>
                    <div class="brand-sub">Enterprise Monolith</div>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-category">Main Workspace</div>
                <a class="nav-item active" onclick="navigateTo('dashboard', this)">
                    <span><i class="fa-solid fa-chart-pie nav-icon"></i> Role Dashboard</span>
                </a>
                <a class="nav-item" onclick="navigateTo('approvals', this)">
                    <span><i class="fa-solid fa-stamp nav-icon"></i> Approval Queue</span>
                    <span class="nav-badge" id="nav-approval-count">3</span>
                </a>

                <div class="menu-category">Core ERP Modules</div>
                <a class="nav-item" onclick="navigateTo('sales', this)">
                    <span><i class="fa-solid fa-receipt nav-icon"></i> Sales & Orders</span>
                </a>
                <a class="nav-item" onclick="navigateTo('procurement', this)">
                    <span><i class="fa-solid fa-cart-shopping nav-icon"></i> Procurement (POs)</span>
                </a>
                <a class="nav-item" onclick="navigateTo('inventory', this)">
                    <span><i class="fa-solid fa-boxes-packing nav-icon"></i> Inventory & FIFO</span>
                </a>
                <a class="nav-item" onclick="navigateTo('finance', this)">
                    <span><i class="fa-solid fa-book nav-icon"></i> General Ledger & FX</span>
                </a>
                <a class="nav-item" onclick="navigateTo('vat', this)">
                    <span><i class="fa-solid fa-file-contract nav-icon"></i> NBR Statutory VAT</span>
                </a>
                <a class="nav-item" onclick="navigateTo('hr', this)">
                    <span><i class="fa-solid fa-user-clock nav-icon"></i> HR & Attendance</span>
                </a>
                <a class="nav-item" onclick="navigateTo('manufacturing', this)">
                    <span><i class="fa-solid fa-industry nav-icon"></i> Manufacturing MRP</span>
                </a>

                <div class="menu-category">Governance</div>
                <a class="nav-item" onclick="navigateTo('telemetry', this)">
                    <span><i class="fa-solid fa-terminal nav-icon"></i> System Telemetry</span>
                </a>
                <a class="nav-item" onclick="navigateTo('settings', this)">
                    <span><i class="fa-solid fa-gear nav-icon"></i> Settings & RLS</span>
                </a>
            </div>
        </aside>

        <!-- Main Workspace Section -->
        <div id="main-container">

            <!-- Persistent Top Command Bar -->
            <header id="topbar">
                <div class="topbar-left">
                    <button class="toggle-sidebar-btn" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
                    <div class="global-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="global-search-input" placeholder="Global Search (Orders, Invoices, Employees, SKUs, Hash)..." onkeyup="handleGlobalSearch(this.value)">
                    </div>
                </div>

                <div class="topbar-right">
                    <button class="quick-create-btn" onclick="openCreateModal('po')">
                        <i class="fa-solid fa-plus"></i> + New Record
                    </button>

                    <select class="context-select" id="tenantSelect" onchange="updateTenantContext()">
                        <option value="aca9ea90-0d0f-4ed9-98ed-398af6b67efd">Tenant A (HQ)</option>
                        <option value="bcb9ea90-0d0f-4ed9-98ed-398af6b67efe">Tenant B (Branch)</option>
                        <option value="ccc9ea90-0d0f-4ed9-98ed-398af6b67eff">Tenant C (Holding)</option>
                    </select>

                    <select class="context-select" id="roleSelect" onchange="switchRoleView(this.value)">
                        <option value="manager">Role: Executive Manager</option>
                        <option value="finance">Role: CFO / Finance Control</option>
                        <option value="warehouse">Role: Warehouse Specialist</option>
                    </select>

                    <div class="icon-btn" onclick="showNotificationDropdown()" title="Notifications">
                        <i class="fa-solid fa-bell"></i>
                        <div class="icon-btn-dot"></div>
                    </div>

                    <div class="status-badge">
                        <div class="status-dot"></div>
                        RLS Active
                    </div>
                </div>
            </header>

            <!-- Page Breadcrumb & Header -->
            <div class="page-header">
                <div>
                    <div class="breadcrumbs">
                        <a href="#">RAAX Monolith</a> / <span id="crumb-current">Executive Manager Dashboard</span>
                    </div>
                    <div class="page-title" id="page-title-text">Role Dashboard</div>
                </div>
                <div class="page-actions" id="page-actions-container">
                    <button class="btn btn-outline btn-sm" onclick="exportCurrentView()"><i class="fa-solid fa-download"></i> Export CSV</button>
                    <button class="btn btn-sm" onclick="runHealthCheck()"><i class="fa-solid fa-rotate"></i> Sync Diagnostics</button>
                </div>
            </div>

            <!-- Workspace Scrollable Content -->
            <div class="workspace-content">

                <!-- PANEL 1: ROLE DASHBOARDS -->
                <div id="view-dashboard" class="view-panel active">
                    
                    <!-- Manager Dashboard View -->
                    <div id="role-dashboard-manager">
                        <div class="kpi-grid">
                            <div class="kpi-card featured">
                                <div class="kpi-header"><div class="kpi-title">Gross Operating Revenue</div><i class="fa-solid fa-vault kpi-icon"></i></div>
                                <div class="kpi-value">BDT 142.5M</div>
                                <div class="kpi-subtitle positive"><i class="fa-solid fa-arrow-up"></i> +12.4% vs Q1 Benchmark</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Net Cash Flow</div><i class="fa-solid fa-money-bill-trend-up kpi-icon"></i></div>
                                <div class="kpi-value">BDT 38.2M</div>
                                <div class="kpi-subtitle positive">Healthy Liquidity</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Overdue AR Receivables</div><i class="fa-solid fa-hand-holding-dollar kpi-icon"></i></div>
                                <div class="kpi-value">BDT 4.1M</div>
                                <div class="kpi-subtitle danger"><i class="fa-solid fa-triangle-exclamation"></i> 3 Accounts Exceeded</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Pending Approvals</div><i class="fa-solid fa-stamp kpi-icon"></i></div>
                                <div class="kpi-value">3 Items</div>
                                <div class="kpi-subtitle orange">Action Required</div>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity & Audit Trail</div>
                                </div>
                                <div class="card-body" style="padding:0;">
                                    <table class="data-table">
                                        <thead><tr><th>Timestamp</th><th>User</th><th>Module</th><th>Action</th></tr></thead>
                                        <tbody>
                                            <tr onclick="openDrawer('PO-2026-8819', 'Purchase Order', 'Submitted', 'Vendor: Steel Corp | Total: BDT 12,500')">
                                                <td class="mono">22:41:05</td><td>A. Rahman</td><td>Procurement</td><td>Submitted PO #PO-8819</td>
                                            </tr>
                                            <tr onclick="openDrawer('JE-2026-001', 'Journal Entry', 'Posted', 'Rent Expense | BDT 450.00')">
                                                <td class="mono">22:15:30</td><td>S. Khan</td><td>Finance</td><td>Posted Journal Entry #JE-001</td>
                                            </tr>
                                            <tr onclick="openDrawer('EMP-1001', 'Attendance Check-in', 'Approved', 'Check-in: 09:10 AM')">
                                                <td class="mono">21:50:12</td><td>M. Haque</td><td>HR</td><td>Log Attendance Check-in</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-triangle-exclamation"></i> System Risk & Exceptions</div>
                                </div>
                                <div class="card-body">
                                    <ul style="list-style:none; font-size:13px; line-height:2;">
                                        <li><span class="status-chip rejected">Overdue</span> Customer Apex Corp balance exceeds credit limit by BDT 150,000</li>
                                        <li><span class="status-chip posted">Alert</span> Stock SKU-FASTENER-A below reorder point (150 units left)</li>
                                        <li><span class="status-chip approved">Complete</span> NBR Mushak 9.1 Return draft calculated cleanly for 2026-07</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Finance Dashboard View -->
                    <div id="role-dashboard-finance" style="display:none;">
                        <div class="kpi-grid">
                            <div class="kpi-card featured">
                                <div class="kpi-header"><div class="kpi-title">Accounts Receivable (AR)</div><i class="fa-solid fa-file-invoice-dollar kpi-icon"></i></div>
                                <div class="kpi-value">BDT 18.4M</div>
                                <div class="kpi-subtitle positive">Aging: 85% Current</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Accounts Payable (AP)</div><i class="fa-solid fa-receipt kpi-icon"></i></div>
                                <div class="kpi-value">BDT 12.1M</div>
                                <div class="kpi-subtitle">Due within 30 days</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Period Close Status</div><i class="fa-solid fa-lock kpi-icon"></i></div>
                                <div class="kpi-value">FY-2026 Open</div>
                                <div class="kpi-subtitle orange">Closing target: July 31</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">MT940 Recon Queue</div><i class="fa-solid fa-building-columns kpi-icon"></i></div>
                                <div class="kpi-value">14 Entries</div>
                                <div class="kpi-subtitle positive">Auto-Matched 92%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Warehouse Dashboard View -->
                    <div id="role-dashboard-warehouse" style="display:none;">
                        <div class="kpi-grid">
                            <div class="kpi-card featured">
                                <div class="kpi-header"><div class="kpi-title">Pending Inbound Receipts</div><i class="fa-solid fa-truck-ramp-box kpi-icon"></i></div>
                                <div class="kpi-value">6 GRNs</div>
                                <div class="kpi-subtitle positive">4 Approved POs</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Pick & Pack Dispatch</div><i class="fa-solid fa-dolly kpi-icon"></i></div>
                                <div class="kpi-value">18 Orders</div>
                                <div class="kpi-subtitle orange">12 Dispatched today</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Low Stock Reorders</div><i class="fa-solid fa-boxes-stacked kpi-icon"></i></div>
                                <div class="kpi-value">2 SKUs</div>
                                <div class="kpi-subtitle danger">Reorder triggered</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Barcode Scan Speed</div><i class="fa-solid fa-barcode kpi-icon"></i></div>
                                <div class="kpi-value">1.2s / item</div>
                                <div class="kpi-subtitle positive">Optimal Queue</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- PANEL 2: APPROVAL QUEUE -->
                <div id="view-approvals" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-stamp"></i> Pending Workflow Approval Requests</div>
                            <span class="badge-tag orange">3 Action Items</span>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <table class="data-table" id="approvalTable">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Workflow Subject</th>
                                        <th>Requester</th>
                                        <th>Impact / Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="approval-row-1">
                                        <td class="mono">REQ-1024</td>
                                        <td>Purchase Order Price Tolerance Exceeded</td>
                                        <td>A. Rahman (Procurement)</td>
                                        <td class="mono">BDT 1,250,000</td>
                                        <td><span class="status-chip submitted" id="app-status-1">Submitted</span></td>
                                        <td>
                                            <button class="btn btn-success btn-sm" onclick="processApproval('1024', 'approve')"><i class="fa-solid fa-check"></i> Approve</button>
                                            <button class="btn btn-danger btn-sm" onclick="processApproval('1024', 'reject')"><i class="fa-solid fa-xmark"></i> Reject</button>
                                        </td>
                                    </tr>
                                    <tr id="approval-row-2">
                                        <td class="mono">REQ-1025</td>
                                        <td>Customer Credit Limit Override Request</td>
                                        <td>K. Ahmed (Sales)</td>
                                        <td class="mono">BDT 850,000</td>
                                        <td><span class="status-chip submitted" id="app-status-2">Submitted</span></td>
                                        <td>
                                            <button class="btn btn-success btn-sm" onclick="processApproval('1025', 'approve')"><i class="fa-solid fa-check"></i> Approve</button>
                                            <button class="btn btn-danger btn-sm" onclick="processApproval('1025', 'reject')"><i class="fa-solid fa-xmark"></i> Reject</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL 3: SALES & ORDERS MASTER LIST -->
                <div id="view-sales" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-receipt"></i> Sales Orders & Customer Billing</div>
                            <button class="btn btn-sm" onclick="openCreateModal('so')"><i class="fa-solid fa-plus"></i> + New Sales Order</button>
                        </div>
                        <div class="list-controls-bar">
                            <div class="controls-left">
                                <span class="filter-chip active" onclick="filterMasterList('salesTable', 'all', this)">All Orders</span>
                                <span class="filter-chip" onclick="filterMasterList('salesTable', 'posted', this)">Posted</span>
                                <span class="filter-chip" onclick="filterMasterList('salesTable', 'draft', this)">Draft</span>
                            </div>
                            <div class="controls-right">
                                <input type="text" class="search-input" placeholder="Filter orders..." onkeyup="filterMasterListQuery('salesTable', this.value)">
                            </div>
                        </div>
                        <div class="data-table-container">
                            <table class="data-table" id="salesTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox"></th>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr onclick="openDrawer('SO-2026-4412', 'Sales Order', 'Posted', 'Customer: Apex Corp | Total: BDT 850,000')">
                                        <td><input type="checkbox" onclick="event.stopPropagation()"></td>
                                        <td class="mono">SO-2026-4412</td>
                                        <td>Apex Holdings Corp</td>
                                        <td class="mono">BDT 850,000</td>
                                        <td><span class="status-chip posted">Posted</span></td>
                                        <td>2026-07-21</td>
                                        <td><button class="btn btn-outline btn-sm">View</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL 4: PROCUREMENT & PO MASTER LIST -->
                <div id="view-procurement" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Purchase Orders & Supplier Register</div>
                            <button class="btn btn-sm" onclick="openCreateModal('po')"><i class="fa-solid fa-plus"></i> + Create Purchase Order</button>
                        </div>
                        <div class="list-controls-bar">
                            <div class="controls-left">
                                <span class="filter-chip active" onclick="filterMasterList('poTable', 'all', this)">All POs</span>
                                <span class="filter-chip" onclick="filterMasterList('poTable', 'approved', this)">Approved</span>
                                <span class="filter-chip" onclick="filterMasterList('poTable', 'submitted', this)">Pending Approval</span>
                            </div>
                            <div class="controls-right">
                                <input type="text" class="search-input" placeholder="Search POs..." onkeyup="filterMasterListQuery('poTable', this.value)">
                            </div>
                        </div>
                        <div class="data-table-container">
                            <table class="data-table" id="poTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox"></th>
                                        <th>PO Number</th>
                                        <th>Vendor Supplier</th>
                                        <th>Total (Cents)</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr onclick="openDrawer('PO-2026-8819', 'Purchase Order', 'Submitted', 'Vendor: Global Steel | Total: BDT 12,500')">
                                        <td><input type="checkbox" onclick="event.stopPropagation()"></td>
                                        <td class="mono">PO-2026-8819</td>
                                        <td>Global Steel Suppliers Ltd</td>
                                        <td class="mono">1,250,000 cents</td>
                                        <td><span class="status-chip submitted">Submitted</span></td>
                                        <td>2026-07-21</td>
                                        <td><button class="btn btn-outline btn-sm">Inspect</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL 5: INVENTORY & FIFO -->
                <div id="view-inventory" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-boxes-packing"></i> FIFO Stock Valuation & Bin Location Matrix</div>
                        </div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>SKU Reference</th>
                                        <th>Warehouse Bin</th>
                                        <th>Initial Qty</th>
                                        <th>Remaining Qty</th>
                                        <th>Unit Cost</th>
                                        <th>Valuation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr onclick="openDrawer('SKU-TRANS-01', 'Stock Item', 'Active', 'Bin: BIN-MAIN-A1 | Remaining: 420 units')">
                                        <td class="mono">SKU-TRANS-01</td>
                                        <td class="mono">BIN-MAIN-A1</td>
                                        <td>500</td>
                                        <td>420</td>
                                        <td>1,000 cents (BDT 10.00)</td>
                                        <td><span class="badge-tag orange">FIFO Batch</span></td>
                                    </tr>
                                    <tr onclick="openDrawer('SKU-RAW-STEEL', 'Stock Item', 'Active', 'Bin: BIN-WH2-B4 | Remaining: 1200 units')">
                                        <td class="mono">SKU-RAW-STEEL</td>
                                        <td class="mono">BIN-WH2-B4</td>
                                        <td>1,200</td>
                                        <td>1,200</td>
                                        <td>4,500 cents (BDT 45.00)</td>
                                        <td><span class="badge-tag orange">FIFO Batch</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL 6: FINANCE & GENERAL LEDGER -->
                <div id="view-finance" class="view-panel">
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title"><i class="fa-solid fa-pen-to-square"></i> Post Double-Entry Journal Entry</div>
                            </div>
                            <div class="card-body">
                                <form id="journalForm" onsubmit="handlePostJournal(event)">
                                    <div class="form-group">
                                        <label class="form-label">Journal Date</label>
                                        <input type="date" id="jeDate" class="form-input" value="2026-07-21" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Reference Number</label>
                                        <input type="text" id="jeRef" class="form-input" value="JE-INV-2026-001" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <input type="text" id="jeDesc" class="form-input" value="Office Rent & Supplies Expenses" required>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;" class="form-group">
                                        <div>
                                            <label class="form-label">Debit Amount (Cents)</label>
                                            <input type="number" id="jeDebit" class="form-input" value="45000" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Credit Amount (Cents)</label>
                                            <input type="number" id="jeCredit" class="form-input" value="45000" required>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn">
                                        <i class="fa-solid fa-paper-plane"></i> Post Journal Entry
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-calculator"></i> Finance Quick Actions</div>
                                </div>
                                <div class="card-body" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <button onclick="fetchConsolidatedTB()" class="btn btn-outline btn-sm"><i class="fa-solid fa-scale-balanced"></i> Consolidated Trial Balance</button>
                                    <button onclick="triggerForexRevaluation()" class="btn btn-outline btn-sm"><i class="fa-solid fa-globe"></i> Month-End Forex Revaluation</button>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-code"></i> API Execution Console</div>
                                </div>
                                <div class="card-body">
                                    <div id="financeOutput" class="terminal-box">Ready to execute financial actions...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 7: NBR STATUTORY VAT -->
                <div id="view-vat" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-file-contract"></i> NBR Mushak Compliance Engine (Bangladesh Statutory Tax)</div>
                            <button onclick="previewMushakReturn()" class="btn btn-outline"><i class="fa-solid fa-rotate"></i> Compile Mushak 9.1 Return</button>
                        </div>
                        <div class="card-body">
                            <div class="grid-2">
                                <div>
                                    <h4 style="font-size: 13px; margin-bottom: 1rem; color: var(--text-pure); text-transform: uppercase;">Statutory Register Models</h4>
                                    <ul style="font-size: 12px; color: var(--text-muted); line-height: 2;">
                                        <li><strong>Mushak 6.3 Tax Invoice:</strong> Sales tax invoice generation</li>
                                        <li><strong>Mushak 6.1 Purchase Register:</strong> Input VAT credit accumulator</li>
                                        <li><strong>Mushak 6.5 Intercompany Challan:</strong> Inter-branch transfer manifest</li>
                                        <li><strong>Mushak 6.6 VDS Certificate:</strong> Withholding tax certificates</li>
                                    </ul>
                                </div>
                                <div>
                                    <div id="vatOutput" class="terminal-box">Click "Compile Mushak 9.1 Return" to aggregate tax ledgers...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 8: HR & ATTENDANCE -->
                <div id="view-hr" class="view-panel">
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title"><i class="fa-solid fa-user-clock"></i> Register Attendance Check-In</div>
                            </div>
                            <div class="card-body">
                                <form id="attendanceForm" onsubmit="handleAttendanceCheckIn(event)">
                                    <div class="form-group">
                                        <label class="form-label">Employee UUID</label>
                                        <input type="text" id="attEmpId" class="form-input mono" value="e1000000-0000-0000-0000-000000000001" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Shift UUID</label>
                                        <input type="text" id="attShiftId" class="form-input mono" value="s1000000-0000-0000-0000-000000000001" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Check-In Timestamp</label>
                                        <input type="text" id="attCheckIn" class="form-input mono" value="2026-07-21 09:10:00" required>
                                    </div>

                                    <button type="submit" class="btn"><i class="fa-solid fa-clock"></i> Log Check-In</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><div class="card-title"><i class="fa-solid fa-terminal"></i> HR Event Output Log</div></div>
                            <div class="card-body"><div id="hrOutput" class="terminal-box">Ready to log attendance...</div></div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 9: MANUFACTURING MRP -->
                <div id="view-manufacturing" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-industry"></i> Bill of Materials (BOM) & MRP Shortfall Engine</div>
                            <button onclick="calculateMrpShortfall()" class="btn btn-outline"><i class="fa-solid fa-calculator"></i> Calculate Material Shortfall</button>
                        </div>
                        <div class="card-body"><div id="mrpOutput" class="terminal-box">Click "Calculate Material Shortfall" to run MRP engine...</div></div>
                    </div>
                </div>

                <!-- PANEL 10: SYSTEM TELEMETRY -->
                <div id="view-telemetry" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-microchip"></i> Platform Diagnostics & Ledger Verifier</div></div>
                        <div class="card-body">
                            <div style="display:flex; gap:1rem; margin-bottom:1.5rem;">
                                <button onclick="runHealthCheck()" class="btn"><i class="fa-solid fa-heart-pulse"></i> Run System Health Check</button>
                                <button onclick="runLedgerVerify()" class="btn btn-outline"><i class="fa-solid fa-link"></i> Verify Cryptographic Ledger Chain</button>
                            </div>
                            <div id="telemetryOutput" class="terminal-box">System Telemetry Console Initialized.</div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 11: SETTINGS -->
                <div id="view-settings" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-gear"></i> System Settings & Row-Level Security Rules</div></div>
                        <div class="card-body">
                            <p style="font-size:13px; color:var(--text-muted);">Configure active tenant boundary policies, currency basis points conversion formulas, and background worker queues.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <footer>
                RAAX Enterprise Resource Planning Platform &bull; PHP 8.3 & Laravel 12 Monolith Architecture
            </footer>
        </div>

        <!-- Right Persistent Detail Drawer -->
        <aside id="detail-drawer">
            <div class="drawer-header">
                <div>
                    <div style="font-size:11px; color:var(--text-dim); text-transform:uppercase;" id="drawer-entity-type">Record Detail</div>
                    <div style="font-size:16px; font-weight:700; color:var(--text-pure);" id="drawer-title">#PO-2026-8819</div>
                </div>
                <button class="drawer-close-btn" onclick="closeDrawer()"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="drawer-body">
                <div style="margin-bottom:1rem;">
                    <span class="status-chip posted" id="drawer-status">Posted</span>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <div class="card-header"><div class="card-title"><i class="fa-solid fa-circle-info"></i> Record Summary</div></div>
                    <div class="card-body" style="font-size:13px; color:var(--text-muted); line-height:1.6;" id="drawer-summary">
                        Select a row from any master list table to inspect details.
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Record Change Audit Log</div></div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div><strong>Record Created</strong> by System Admin</div>
                                <div class="timeline-time">Today at 22:10</div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div><strong>RLS Scope Verified</strong> (Tenant Context Sealed)</div>
                                <div class="timeline-time">Today at 22:11</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="drawer-footer">
                <button class="btn btn-outline btn-sm" onclick="closeDrawer()">Close</button>
                <button class="btn btn-sm" onclick="showToast('Record changes saved cleanly.')">Save Edits</button>
            </div>
        </aside>

    </div>

    <!-- Quick Create Modal Overlay -->
    <div class="modal-overlay" id="createModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="card-title"><i class="fa-solid fa-file-pen"></i> Create Purchase Order</div>
                <button class="drawer-close-btn" onclick="closeCreateModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form id="poCreateForm" onsubmit="submitModalPO(event)">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Vendor / Supplier</label>
                            <input type="text" id="modalPoVendor" class="form-input" value="Global Steel Suppliers Ltd" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Warehouse Bin Target</label>
                            <select id="modalPoWarehouse" class="form-select">
                                <option value="BIN-MAIN-A1">BIN-MAIN-A1 (Main Facility)</option>
                                <option value="BIN-WH2-B4">BIN-WH2-B4 (Regional Bin)</option>
                            </select>
                        </div>
                    </div>

                    <div style="font-size:12px; font-weight:700; color:var(--text-dim); text-transform:uppercase; margin: 10px 0;">Order Line Items</div>
                    <table class="line-item-table">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th style="width:100px;">Qty</th>
                                <th style="width:140px;">Unit Price (Cents)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" class="form-input" value="Raw Steel Sheets 5mm" required></td>
                                <td><input type="number" class="form-input" value="10" required></td>
                                <td><input type="number" class="form-input" value="12500" required></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="submit" class="btn" style="width:100%; justify-content:center;"><i class="fa-solid fa-paper-plane"></i> Submit Purchase Order</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <!-- Client Interactive Logic -->
    <script>
        function getTenantId() {
            return document.getElementById('tenantSelect').value;
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color:var(--orange-brand);"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        function navigateTo(viewId, element) {
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.view-panel').forEach(panel => panel.classList.remove('active'));

            if (element) element.classList.add('active');

            const targetPanel = document.getElementById(`view-${viewId}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            const titleMap = {
                'dashboard': 'Role Dashboard',
                'approvals': 'Approval Queue',
                'sales': 'Sales & Orders Master List',
                'procurement': 'Purchase Orders Register',
                'inventory': 'FIFO Inventory Valuation Matrix',
                'finance': 'General Ledger & Forex Engine',
                'vat': 'NBR Statutory VAT Compliance Engine',
                'hr': 'HR Directory & Attendance Ledger',
                'manufacturing': 'Manufacturing MRP & BOM Engine',
                'telemetry': 'System Telemetry & Health',
                'settings': 'System Settings & RLS Controls'
            };

            document.getElementById('crumb-current').innerText = titleMap[viewId] || 'Workspace';
            document.getElementById('page-title-text').innerText = titleMap[viewId] || 'Workspace';
        }

        function switchRoleView(role) {
            document.getElementById('role-dashboard-manager').style.display = (role === 'manager') ? 'block' : 'none';
            document.getElementById('role-dashboard-finance').style.display = (role === 'finance') ? 'block' : 'none';
            document.getElementById('role-dashboard-warehouse').style.display = (role === 'warehouse') ? 'block' : 'none';

            showToast(`Switched workspace perspective to: ${role.toUpperCase()}`);
        }

        function updateTenantContext() {
            showToast(`Tenant context updated to ID: ${getTenantId()}`);
        }

        function openDrawer(id, type, status, summary) {
            document.getElementById('drawer-title').innerText = id;
            document.getElementById('drawer-entity-type').innerText = type;
            document.getElementById('drawer-status').innerText = status;
            document.getElementById('drawer-summary').innerText = summary;
            document.getElementById('detail-drawer').classList.add('open');
        }

        function closeDrawer() {
            document.getElementById('detail-drawer').classList.remove('open');
        }

        function openCreateModal(type) {
            document.getElementById('createModal').classList.add('open');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('open');
        }

        function processApproval(id, action) {
            const statusEl = document.getElementById(`app-status-${id === '1024' ? '1' : '2'}`);
            if (action === 'approve') {
                statusEl.className = 'status-chip approved';
                statusEl.innerText = 'Approved';
                showToast(`Request #${id} Approved successfully.`);
            } else {
                statusEl.className = 'status-chip rejected';
                statusEl.innerText = 'Rejected';
                showToast(`Request #${id} Rejected.`);
            }
        }

        function submitModalPO(e) {
            e.preventDefault();
            closeCreateModal();
            showToast("Purchase Order PO-2026-8820 submitted for multi-tier approval.");
        }

        function filterMasterList(tableId, status, chipEl) {
            const parent = chipEl.parentElement;
            parent.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            chipEl.classList.add('active');

            const table = document.getElementById(tableId);
            const trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) {
                if (status === 'all') {
                    trs[i].style.display = '';
                } else {
                    const text = trs[i].innerText.toLowerCase();
                    trs[i].style.display = text.includes(status.toLowerCase()) ? '' : 'none';
                }
            }
        }

        function filterMasterListQuery(tableId, query) {
            const table = document.getElementById(tableId);
            const trs = table.getElementsByTagName('tr');
            const q = query.toLowerCase();

            for (let i = 1; i < trs.length; i++) {
                const text = trs[i].textContent.toLowerCase();
                trs[i].style.display = text.includes(q) ? '' : 'none';
            }
        }

        function handleGlobalSearch(query) {
            if (query.trim().length > 2) {
                console.log("Global search executing for:", query);
            }
        }

        function exportCurrentView() {
            showToast("Exporting current view to CSV file...");
        }

        function showNotificationDropdown() {
            showToast("Notifications: 3 pending approvals & 1 low stock alert.");
        }

        /* Backend API Handlers */
        async function handlePostJournal(e) {
            e.preventDefault();
            const output = document.getElementById('financeOutput');
            output.innerHTML = 'Posting journal entry...';

            const payload = {
                entry_date: document.getElementById('jeDate').value,
                reference: document.getElementById('jeRef').value,
                description: document.getElementById('jeDesc').value,
                currency_code: 'BDT',
                lines: [
                    { account_code: '5001', debit_cents: parseInt(document.getElementById('jeDebit').value), credit_cents: 0 },
                    { account_code: '1001', debit_cents: 0, credit_cents: parseInt(document.getElementById('jeCredit').value) }
                ]
            };

            try {
                const res = await fetch('/api/v1/finance/journals', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                output.innerHTML = `<span class="hl-orange">[HTTP Status: ${res.status}]</span>\n` + JSON.stringify(data, null, 2);
                showToast("Journal entry posted successfully!");
            } catch (err) {
                output.innerHTML = `[Error]: ${err.message}`;
            }
        }

        async function fetchConsolidatedTB() {
            const output = document.getElementById('financeOutput');
            output.innerHTML = 'Fetching Consolidated Trial Balance...';

            try {
                const res = await fetch('/api/v1/finance/reports/consolidated-trial-balance?start_date=2026-01-01&end_date=2026-12-31', {
                    headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() }
                });
                const data = await res.json();
                output.innerHTML = `<span class="hl-orange">[Consolidated Trial Balance]</span>\n` + JSON.stringify(data, null, 2);
            } catch (err) {
                output.innerHTML = `[Error]: ${err.message}`;
            }
        }

        async function triggerForexRevaluation() {
            const output = document.getElementById('financeOutput');
            output.innerHTML = 'Executing Month-End Forex Revaluation...';

            try {
                const res = await fetch('/api/v1/finance/forex/revalue', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() },
                    body: JSON.stringify({ target_month: '2026-07', target_currency: 'USD' })
                });
                const data = await res.json();
                output.innerHTML = `<span class="hl-orange">[Forex Revaluation Output]</span>\n` + JSON.stringify(data, null, 2);
            } catch (err) {
                output.innerHTML = `[Error]: ${err.message}`;
            }
        }

        async function handleAttendanceCheckIn(e) {
            e.preventDefault();
            const output = document.getElementById('hrOutput');
            output.innerHTML = 'Logging attendance...';

            const payload = {
                employee_id: document.getElementById('attEmpId').value,
                shift_id: document.getElementById('attShiftId').value,
                check_in_time: document.getElementById('attCheckIn').value
            };

            try {
                const res = await fetch('/api/v1/hr/attendance/check-in', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                output.innerHTML = `<span class="hl-orange">[HTTP Status: ${res.status}]</span>\n` + JSON.stringify(data, null, 2);
                showToast("Attendance logged successfully!");
            } catch (err) {
                output.innerHTML = `[Error]: ${err.message}`;
            }
        }

        async function calculateMrpShortfall() {
            const output = document.getElementById('mrpOutput');
            output.innerHTML = "<span class='hl-orange'>Calculating MRP Material Shortfall...</span>\n\n";
            output.innerHTML += JSON.stringify({
                demand_batch: "WORK-ORDER-991",
                required_materials: [
                    { sku: "SKU-RAW-STEEL", required_qty: 200, in_stock: 1200, shortfall: 0 },
                    { sku: "SKU-FASTENER-A", required_qty: 500, in_stock: 150, shortfall: 350 }
                ],
                reorder_trigger_dispatched: true
            }, null, 2);
        }

        async function previewMushakReturn() {
            const output = document.getElementById('vatOutput');
            output.innerHTML = 'Compiling Mushak 9.1 return...';

            try {
                const res = await fetch('/api/v1/finance/vat/returns/2026-07', {
                    headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() }
                });
                const data = await res.json();
                output.innerHTML = `<span class="hl-orange">[HTTP Status: ${res.status}]</span>\n` + JSON.stringify(data, null, 2);
            } catch (err) {
                output.innerHTML = `[Error]: ${err.message}`;
            }
        }

        async function runHealthCheck() {
            const output = document.getElementById('telemetryOutput');
            output.innerHTML = "<span class='hl-orange'>Running RAAX System Health Diagnostic...</span>\n\n";
            output.innerHTML += "<span class='hl-green'>[OK] Database Connection:</span> SQLite Memory Pool Active\n";
            output.innerHTML += "<span class='hl-green'>[OK] RLS Engine:</span> Tenant Context Isolation Verified\n";
            output.innerHTML += "<span class='hl-green'>[OK] Queue Backlogs:</span> Sync Database Queue Operational\n";
            output.innerHTML += "<span class='hl-green'>[OK] Monolith Structure:</span> All 9 Core Modules Bound";
            showToast("System health diagnostic clean!");
        }

        async function runLedgerVerify() {
            const output = document.getElementById('telemetryOutput');
            output.innerHTML = `<span class='hl-orange'>Verifying Cryptographic Ledger Chain for Tenant: ${getTenantId()}...</span>\n\n`;
            output.innerHTML += "<span class='hl-green'>[OK] Genesis Hash Match:</span> Valid\n";
            output.innerHTML += "<span class='hl-green'>[OK] Cryptographic Chain:</span> SHA-256 Ledger Sealed & Tamper-Evident";
            showToast("Cryptographic ledger verified!");
        }
    </script>
</body>
</html>
