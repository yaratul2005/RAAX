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
            --drawer-width: 480px;
            --sidebar-width: 250px;
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

        /* Before/After Audit Diff Box */
        .diff-box {
            background: #09090b;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 10px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            margin-top: 8px;
        }

        .diff-old { color: var(--status-red); text-decoration: line-through; }
        .diff-new { color: var(--status-green); }

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

        /* Toast Container */
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
                    <div class="brand-sub">Enterprise Core</div>
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

                <div class="menu-category">Mandatory Core Modules</div>
                <a class="nav-item" onclick="navigateTo('sales', this)">
                    <span><i class="fa-solid fa-receipt nav-icon"></i> Sales & Billing</span>
                </a>
                <a class="nav-item" onclick="navigateTo('procurement', this)">
                    <span><i class="fa-solid fa-cart-shopping nav-icon"></i> Procurement (POs)</span>
                </a>
                <a class="nav-item" onclick="navigateTo('inventory', this)">
                    <span><i class="fa-solid fa-boxes-packing nav-icon"></i> Inventory & FIFO</span>
                </a>
                <a class="nav-item" onclick="navigateTo('stock-adjust', this)">
                    <span><i class="fa-solid fa-sliders nav-icon"></i> Stock Adjustments</span>
                </a>
                <a class="nav-item" onclick="navigateTo('finance', this)">
                    <span><i class="fa-solid fa-book nav-icon"></i> Finance & GL</span>
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

                <div class="menu-category">Governance & Controls</div>
                <a class="nav-item" onclick="navigateTo('audit', this)">
                    <span><i class="fa-solid fa-history nav-icon"></i> Before/After Audit</span>
                </a>
                <a class="nav-item" onclick="navigateTo('sod', this)">
                    <span><i class="fa-solid fa-user-lock nav-icon"></i> Segregation of Duties</span>
                </a>
                <a class="nav-item" onclick="navigateTo('telemetry', this)">
                    <span><i class="fa-solid fa-terminal nav-icon"></i> System Telemetry</span>
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
                        <input type="text" class="global-search-input" placeholder="Global Search (POs, Invoices, Stock, Audit Hashes)..." onkeyup="handleGlobalSearch(this.value)">
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
                        RLS Sealed
                    </div>
                </div>
            </header>

            <!-- Page Header / Breadcrumbs -->
            <div class="page-header">
                <div>
                    <div class="breadcrumbs">
                        <a href="#">RAAX Monolith</a> / <span id="crumb-current">Role Dashboard</span>
                    </div>
                    <div class="page-title" id="page-title-text">Role Dashboard</div>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline btn-sm" onclick="exportCurrentView()"><i class="fa-solid fa-download"></i> Export CSV</button>
                    <button class="btn btn-sm" onclick="runHealthCheck()"><i class="fa-solid fa-rotate"></i> Sync Health</button>
                </div>
            </div>

            <!-- Workspace Scrollable Content -->
            <div class="workspace-content">

                <!-- PANEL 1: ROLE DASHBOARDS -->
                <div id="view-dashboard" class="view-panel active">
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
                                <div class="card-header"><div class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Audit Trail Activity</div></div>
                                <div class="card-body" style="padding:0;">
                                    <table class="data-table">
                                        <thead><tr><th>Timestamp</th><th>User</th><th>Module</th><th>Action & Before/After Value</th></tr></thead>
                                        <tbody>
                                            <tr onclick="openDrawer('ADJ-9912', 'Stock Adjustment', 'Approved', 'SKU-FASTENER-A: 500 -> 150 (Shrinkage)')">
                                                <td class="mono">22:41:05</td><td>A. Rahman</td><td>Inventory</td><td>Stock Adjust: 500 &rarr; 150 units</td>
                                            </tr>
                                            <tr onclick="openDrawer('JE-2026-001', 'Journal Entry', 'Posted', 'Rent Expense: BDT 450.00')">
                                                <td class="mono">22:15:30</td><td>S. Khan</td><td>Finance</td><td>Posted Journal Entry #JE-001</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><div class="card-title"><i class="fa-solid fa-user-lock"></i> Segregation of Duties (SoD) Compliance</div></div>
                                <div class="card-body">
                                    <ul style="list-style:none; font-size:13px; line-height:2;">
                                        <li><span class="status-chip approved">Enforced</span> Maker/Checker rule active for Purchase Orders & Payment Vouchers</li>
                                        <li><span class="status-chip approved">Enforced</span> Creator cannot approve their own Stock Adjustment</li>
                                        <li><span class="status-chip approved">Enforced</span> RLS Session boundary locked to active tenant context</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="role-dashboard-finance" style="display:none;">
                        <div class="kpi-grid">
                            <div class="kpi-card featured">
                                <div class="kpi-header"><div class="kpi-title">Accounts Receivable</div><i class="fa-solid fa-file-invoice-dollar kpi-icon"></i></div>
                                <div class="kpi-value">BDT 18.4M</div>
                                <div class="kpi-subtitle positive">Aging: 85% Current</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Accounts Payable</div><i class="fa-solid fa-receipt kpi-icon"></i></div>
                                <div class="kpi-value">BDT 12.1M</div>
                                <div class="kpi-subtitle">Due in 30 days</div>
                            </div>
                        </div>
                    </div>

                    <div id="role-dashboard-warehouse" style="display:none;">
                        <div class="kpi-grid">
                            <div class="kpi-card featured">
                                <div class="kpi-header"><div class="kpi-title">Pending Inbound Receipts</div><i class="fa-solid fa-truck-ramp-box kpi-icon"></i></div>
                                <div class="kpi-value">6 GRNs</div>
                                <div class="kpi-subtitle positive">4 Approved POs</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-header"><div class="kpi-title">Barcode Scan Speed</div><i class="fa-solid fa-barcode kpi-icon"></i></div>
                                <div class="kpi-value">1.2s / item</div>
                                <div class="kpi-subtitle positive">Optimal Queue</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: APPROVAL QUEUE (MANDATORY CONTROL) -->
                <div id="view-approvals" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-stamp"></i> Pending Workflow Approval Requests</div>
                            <span class="badge-tag orange">Mandatory Governance</span>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <table class="data-table" id="approvalTable">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Workflow Type</th>
                                        <th>Requester</th>
                                        <th>Impact Value</th>
                                        <th>SoD Validation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="approval-row-1">
                                        <td class="mono">REQ-1024</td>
                                        <td>Purchase Order Price Tolerance Exceeded</td>
                                        <td>A. Rahman (Procurement)</td>
                                        <td class="mono">BDT 1,250,000</td>
                                        <td><span class="badge-tag green">SoD Pass (Different User)</span></td>
                                        <td>
                                            <button class="btn btn-success btn-sm" onclick="processApproval('1024', 'approve')"><i class="fa-solid fa-check"></i> Approve</button>
                                            <button class="btn btn-danger btn-sm" onclick="processApproval('1024', 'reject')"><i class="fa-solid fa-xmark"></i> Reject</button>
                                        </td>
                                    </tr>
                                    <tr id="approval-row-2">
                                        <td class="mono">ADJ-9912</td>
                                        <td>Stock Adjustment Request (Shrinkage)</td>
                                        <td>W. Floor Clerk</td>
                                        <td class="mono">350 units (BDT 15,750)</td>
                                        <td><span class="badge-tag green">SoD Pass</span></td>
                                        <td>
                                            <button class="btn btn-success btn-sm" onclick="processApproval('9912', 'approve')"><i class="fa-solid fa-check"></i> Approve Adjustment</button>
                                            <button class="btn btn-danger btn-sm" onclick="processApproval('9912', 'reject')"><i class="fa-solid fa-xmark"></i> Reject</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL 3: MANDATORY STOCK ADJUSTMENTS -->
                <div id="view-stock-adjust" class="view-panel">
                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title"><i class="fa-solid fa-sliders"></i> Request Mandatory Stock Adjustment</div>
                            </div>
                            <div class="card-body">
                                <form onsubmit="handleStockAdjustment(event)">
                                    <div class="form-group">
                                        <label class="form-label">SKU Item Reference</label>
                                        <select id="adjSku" class="form-select">
                                            <option value="SKU-FASTENER-A">SKU-FASTENER-A (Current: 500 units)</option>
                                            <option value="SKU-RAW-STEEL">SKU-RAW-STEEL (Current: 1,200 units)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New Physical Quantity</label>
                                        <input type="number" id="adjNewQty" class="form-input" value="150" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Adjustment Reason Code</label>
                                        <select id="adjReason" class="form-select">
                                            <option value="shrinkage">Shrinkage / Physical Count Discrepancy</option>
                                            <option value="damage">Damaged Stock Write-off</option>
                                            <option value="audit">Audit Variance Re-alignment</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Submit Stock Adjustment for Approval</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><div class="card-title"><i class="fa-solid fa-terminal"></i> Adjustment Audit Log Output</div></div>
                            <div class="card-body"><div id="adjOutput" class="terminal-box">Ready to submit stock adjustments...</div></div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 4: BEFORE / AFTER AUDIT TRAIL (MANDATORY CONTROL) -->
                <div id="view-audit" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-history"></i> Complete System Audit Trail with Before/After Diff Values</div>
                            <span class="badge-tag orange">Immutable Audit Log</span>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Log ID</th>
                                        <th>Timestamp</th>
                                        <th>User & IP</th>
                                        <th>Entity</th>
                                        <th>Action</th>
                                        <th>Before / After Values</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="mono">AUD-8821</td>
                                        <td class="mono">2026-07-23 22:41</td>
                                        <td>A. Rahman (192.168.1.45)</td>
                                        <td>stock_items</td>
                                        <td>UPDATE</td>
                                        <td>
                                            <div class="diff-box">
                                                <span class="diff-old">- "qty": 500</span><br>
                                                <span class="diff-new">+ "qty": 150 (Reason: Shrinkage)</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="mono">AUD-8820</td>
                                        <td class="mono">2026-07-23 22:15</td>
                                        <td>S. Khan (192.168.1.12)</td>
                                        <td>journal_entries</td>
                                        <td>INSERT</td>
                                        <td>
                                            <div class="diff-box">
                                                <span class="diff-new">+ "reference": "JE-INV-2026-001", "debit_cents": 45000</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PANEL 5: SEGREGATION OF DUTIES MATRIX -->
                <div id="view-sod" class="view-panel">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title"><i class="fa-solid fa-user-lock"></i> Segregation of Duties (SoD) Enforcement Matrix</div>
                        </div>
                        <div class="card-body" style="padding:0;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Workflow Path</th>
                                        <th>Initiator Role</th>
                                        <th>Approver / Checker Role</th>
                                        <th>SoD Conflict Check</th>
                                        <th>Rule Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Purchase Order Approval</td>
                                        <td>Procurement Officer</td>
                                        <td>CFO / Finance Admin</td>
                                        <td>Creator $\neq$ Approver</td>
                                        <td><span class="badge-tag green">Strictly Enforced</span></td>
                                    </tr>
                                    <tr>
                                        <td>Stock Adjustment Write-off</td>
                                        <td>Warehouse Clerk</td>
                                        <td>Warehouse Manager</td>
                                        <td>Clerk $\neq$ Approver</td>
                                        <td><span class="badge-tag green">Strictly Enforced</span></td>
                                    </tr>
                                    <tr>
                                        <td>Payment Voucher Disbursement</td>
                                        <td>AP Specialist</td>
                                        <td>Treasury Officer</td>
                                        <td>Disburser $\neq$ Approver</td>
                                        <td><span class="badge-tag green">Strictly Enforced</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- OTHER PANELS (SALES, PROCUREMENT, INVENTORY, FINANCE, VAT, HR, MRP, TELEMETRY, SETTINGS) -->
                <div id="view-sales" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-receipt"></i> Sales Orders & Customer Records</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Sales Order management with automatic customer credit limit checks and discount approval thresholds.</p></div>
                    </div>
                </div>

                <div id="view-procurement" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Purchase Orders & Requisitions</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">PO management with 3-way matching and multi-tier approval routing.</p></div>
                    </div>
                </div>

                <div id="view-inventory" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-boxes-packing"></i> Inventory & FIFO Batch Valuation</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Multi-warehouse stock control with FIFO valuation and batch reorder points.</p></div>
                    </div>
                </div>

                <div id="view-finance" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-book"></i> General Ledger & Financial Reporting</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Double-entry GL, MT940 bank reconciliation, and forex revaluation.</p></div>
                    </div>
                </div>

                <div id="view-vat" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-file-contract"></i> NBR Statutory VAT (Mushak Compliance)</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Mushak 6.1, 6.3, 6.5, 6.6, and 9.1 Return compiler.</p></div>
                    </div>
                </div>

                <div id="view-hr" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-user-clock"></i> HR Directory & Attendance Ledger</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Employee master, shift scheduling, and attendance worked minutes logging.</p></div>
                    </div>
                </div>

                <div id="view-manufacturing" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-industry"></i> Manufacturing MRP & BOM Engine</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Bill of Materials (BOM) multi-level trees and MRP material shortfall calculations.</p></div>
                    </div>
                </div>

                <div id="view-telemetry" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-terminal"></i> System Telemetry & Health</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">Platform health checks (`raax:health`) and cryptographic ledger verification.</p></div>
                    </div>
                </div>

                <div id="view-settings" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-gear"></i> System Configuration & RLS</div></div>
                        <div class="card-body"><p style="font-size:13px; color:var(--text-muted);">PostgreSQL Row-Level Security policy configurations and tenant management.</p></div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <footer>
                RAAX Enterprise Resource Planning Platform &bull; PHP 8.3 & Laravel 12 Monolith Architecture
            </footer>
        </div>

        <!-- Persistent Right Detail Drawer -->
        <aside id="detail-drawer">
            <div class="drawer-header">
                <div>
                    <div style="font-size:11px; color:var(--text-dim); text-transform:uppercase;" id="drawer-entity-type">Record Inspection</div>
                    <div style="font-size:16px; font-weight:700; color:var(--text-pure);" id="drawer-title">#ADJ-9912</div>
                </div>
                <button class="drawer-close-btn" onclick="closeDrawer()"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="drawer-body">
                <div style="margin-bottom:1rem;">
                    <span class="status-chip posted" id="drawer-status">Approved</span>
                </div>

                <div class="card" style="margin-bottom:1rem;">
                    <div class="card-header"><div class="card-title"><i class="fa-solid fa-circle-info"></i> Record Summary & Audit Diff</div></div>
                    <div class="card-body" style="font-size:13px; color:var(--text-muted); line-height:1.6;" id="drawer-summary">
                        SKU-FASTENER-A: 500 &rarr; 150 units (Shrinkage write-off).
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><div class="card-title"><i class="fa-solid fa-paperclip"></i> Documents & Attachments</div></div>
                    <div class="card-body">
                        <div style="font-size:12px; color:var(--text-muted);">
                            <i class="fa-solid fa-file-pdf" style="color:var(--orange-brand);"></i> physical_stock_audit_count.pdf (1.2 MB)
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
                            <label class="form-label">Vendor Supplier</label>
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
                'approvals': 'Approval Queue (Mandatory Control)',
                'stock-adjust': 'Stock Adjustments (Mandatory Control)',
                'audit': 'Before / After Audit Trail',
                'sod': 'Segregation of Duties (SoD) Matrix',
                'sales': 'Sales & Orders Master List',
                'procurement': 'Purchase Orders Register',
                'inventory': 'FIFO Inventory Valuation',
                'finance': 'General Ledger & Forex Engine',
                'vat': 'NBR Statutory VAT Engine',
                'hr': 'HR Directory & Attendance Ledger',
                'manufacturing': 'Manufacturing MRP & BOM Engine',
                'telemetry': 'System Telemetry & Health',
                'settings': 'System Settings & RLS'
            };

            document.getElementById('crumb-current').innerText = titleMap[viewId] || 'Workspace';
            document.getElementById('page-title-text').innerText = titleMap[viewId] || 'Workspace';
        }

        function switchRoleView(role) {
            document.getElementById('role-dashboard-manager').style.display = (role === 'manager') ? 'block' : 'none';
            document.getElementById('role-dashboard-finance').style.display = (role === 'finance') ? 'block' : 'none';
            document.getElementById('role-dashboard-warehouse').style.display = (role === 'warehouse') ? 'block' : 'none';

            showToast(`Workspace role perspective: ${role.toUpperCase()}`);
        }

        function updateTenantContext() {
            showToast(`Tenant context updated to: ${getTenantId()}`);
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
            if (action === 'approve') {
                showToast(`Request #${id} Approved cleanly.`);
            } else {
                showToast(`Request #${id} Rejected.`);
            }
        }

        function handleStockAdjustment(e) {
            e.preventDefault();
            const sku = document.getElementById('adjSku').value;
            const newQty = document.getElementById('adjNewQty').value;
            const reason = document.getElementById('adjReason').value;

            const output = document.getElementById('adjOutput');
            output.innerHTML = `<span class="hl-orange">[Stock Adjustment Submitted for Approval]</span>\n` + JSON.stringify({
                adjustment_id: "ADJ-2026-9913",
                sku: sku,
                new_qty: parseInt(newQty),
                reason_code: reason,
                sod_check: "passed",
                status: "pending_manager_approval"
            }, null, 2);

            showToast("Stock adjustment request created with audit log!");
        }

        function submitModalPO(e) {
            e.preventDefault();
            closeCreateModal();
            showToast("Purchase Order submitted for multi-tier approval.");
        }

        function runHealthCheck() {
            showToast("System health diagnostic clean!");
        }

        async function loadModuleRegistry() {
            try {
                const res = await fetch('/api/v1/system/modules', {
                    headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() }
                });
                const data = await res.json();
                console.log("[Layered Core Backend] Registered Modules & Config:", data);
            } catch (err) {
                console.warn("Module registry fetch warning:", err);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadModuleRegistry();
        });
    </script>
</body>
</html>
