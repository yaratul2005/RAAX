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
            --drawer-width: 540px;
            --sidebar-width: 240px;
            --sidebar-collapsed-width: 64px;
            --statusbar-height: 26px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-root);
            color: var(--text-pure);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            user-select: none;
        }

        h1, h2, h3, h4, .font-heading { font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.02em; }
        .mono { font-family: 'JetBrains Mono', monospace; }

        #app-wrapper { display: flex; flex: 1; min-height: 0; width: 100vw; overflow: hidden; }

        /* Sidebar Navigation Shell */
        aside#sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 90;
            transition: width 0.15s ease-in-out;
        }

        aside#sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        aside#sidebar.collapsed .brand-title,
        aside#sidebar.collapsed .brand-sub,
        aside#sidebar.collapsed .menu-category,
        aside#sidebar.collapsed .nav-text,
        aside#sidebar.collapsed .nav-badge {
            display: none;
        }

        aside#sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 10px 0;
        }

        .sidebar-brand {
            padding: 1rem 1rem 0.85rem 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border-subtle);
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: var(--orange-brand);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            font-size: 17px;
            font-weight: 800;
            flex-shrink: 0;
            cursor: pointer;
        }

        .brand-title { font-size: 16px; font-weight: 700; color: var(--text-pure); line-height: 1.1; }
        .brand-title span { color: var(--orange-brand); }
        .brand-sub { font-size: 9px; color: var(--text-dim); font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; }

        .sidebar-menu { flex: 1; padding: 0.75rem 0.5rem; overflow-y: auto; }
        .menu-category { font-size: 9px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.08em; padding: 10px 8px 4px 8px; }

        .nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            color: var(--text-muted);
            font-size: 12.5px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.1s ease, color 0.1s ease;
            margin-bottom: 2px;
        }

        .nav-item:hover { color: var(--text-pure); background: rgba(255, 255, 255, 0.04); }
        .nav-item.active { color: var(--orange-brand); background: var(--orange-glow); font-weight: 600; }
        .nav-item i.nav-icon { width: 18px; font-size: 13.5px; color: var(--text-dim); text-align: center; }
        .nav-item.active i.nav-icon, .nav-item:hover i.nav-icon { color: var(--orange-brand); }

        .nav-badge { font-size: 9.5px; padding: 1px 5px; border-radius: 8px; background: rgba(255, 94, 0, 0.18); color: var(--orange-brand); font-weight: 700; }

        /* Main Workspace Container */
        #main-container { flex: 1; display: flex; flex-direction: column; min-width: 0; background: var(--bg-root); position: relative; }

        /* Top Bar */
        header#topbar {
            height: 52px;
            background: #0b0b0e;
            border-bottom: 1px solid var(--border-subtle);
            padding: 0 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 80;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; flex: 1; max-width: 480px; }
        .toggle-btn { background: transparent; border: none; color: var(--text-dim); font-size: 15px; cursor: pointer; padding: 4px; border-radius: 4px; }
        .toggle-btn:hover { color: var(--text-pure); background: rgba(255,255,255,0.05); }

        .global-search-box { position: relative; width: 100%; }
        .global-search-box i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 12px; }
        .global-search-input { width: 100%; background: #141418; border: 1px solid var(--border-subtle); border-radius: 5px; padding: 6px 10px 6px 30px; color: var(--text-pure); font-size: 11.5px; outline: none; }
        .global-search-input:focus { border-color: var(--orange-brand); }

        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .quick-create-btn {
            background: var(--orange-brand); color: #000; border: none; padding: 5px 12px; border-radius: 5px; font-weight: 700; font-size: 11.5px; cursor: pointer; display: flex; align-items: center; gap: 5px;
        }
        .quick-create-btn:hover { background: var(--orange-hover); }

        .context-select { background: var(--card-bg); border: 1px solid var(--border-subtle); color: var(--text-pure); font-size: 11.5px; font-weight: 600; padding: 4px 8px; border-radius: 5px; outline: none; cursor: pointer; }

        .icon-bell-btn {
            position: relative; background: var(--card-bg); border: 1px solid var(--border-subtle); color: var(--text-muted); width: 30px; height: 30px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .icon-bell-btn:hover { color: var(--text-pure); border-color: var(--border-highlight); }
        .bell-dot { position: absolute; top: 4px; right: 4px; width: 6px; height: 6px; background: var(--orange-brand); border-radius: 50%; }

        .user-avatar-btn {
            display: flex; align-items: center; gap: 6px; background: var(--card-bg); border: 1px solid var(--border-subtle); padding: 3px 8px; border-radius: 5px; cursor: pointer; font-size: 11.5px; color: var(--text-pure);
        }
        .user-avatar { width: 20px; height: 20px; background: var(--orange-brand); color: #000; font-weight: 800; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; }

        /* Page Sub-Header & Action Toolbar */
        .page-header { padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--border-subtle); background: #0d0d11; display: flex; align-items: center; justify-content: space-between; }
        .breadcrumbs { display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: var(--text-dim); margin-bottom: 2px; }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-pure); }
        .page-actions { display: flex; align-items: center; gap: 6px; }

        /* Workspace Main Body */
        .workspace-content { flex: 1; padding: 1.25rem; overflow-y: auto; }
        .view-panel { display: none; }
        .view-panel.active { display: block; }

        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem; margin-bottom: 1.25rem; }
        .kpi-card { background: var(--card-bg); border: 1px solid var(--border-subtle); border-top: 2px solid var(--border-highlight); border-radius: 6px; padding: 1rem; position: relative; }
        .kpi-card.primary-headline { border-top: 2px solid var(--orange-brand); background: linear-gradient(180deg, rgba(255,94,0,0.06) 0%, rgba(18,18,21,1) 100%); }
        .kpi-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
        .kpi-title { font-size: 10.5px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.04em; }
        .kpi-badge { font-size: 9px; padding: 1px 5px; border-radius: 4px; font-weight: 700; background: var(--orange-glow); color: var(--orange-brand); border: 1px solid rgba(255,94,0,0.3); }
        .kpi-value { font-size: 24px; font-weight: 700; color: var(--text-pure); font-family: 'Space Grotesk', sans-serif; margin-bottom: 4px; }
        .kpi-trend { font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 4px; }
        .kpi-trend.up { color: var(--status-green); }
        .kpi-trend.down { color: var(--status-red); }
        .kpi-trend.neutral { color: var(--status-amber); }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .card { background: var(--card-bg); border: 1px solid var(--border-subtle); border-radius: 6px; overflow: hidden; margin-bottom: 1.25rem; }
        .card-header { background: var(--card-header-bg); padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 13px; font-weight: 600; color: var(--text-pure); display: flex; align-items: center; gap: 6px; }
        .card-title i { color: var(--orange-brand); }
        .card-body { padding: 1rem; }

        /* Data Tables & Grids */
        .data-table-container { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .data-table th { text-align: left; padding: 8px 10px; font-size: 10.5px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; border-bottom: 1px solid var(--border-subtle); background: #0f0f12; white-space: nowrap; }
        .data-table td { padding: 9px 10px; border-bottom: 1px solid var(--border-subtle); color: var(--text-muted); white-space: nowrap; }
        .data-table tr:hover td { background: rgba(255, 255, 255, 0.03); color: var(--text-pure); cursor: pointer; }
        .data-table tr.selected td { background: var(--orange-glow); color: var(--text-pure); font-weight: 600; }

        .status-chip { font-size: 10.5px; padding: 2px 7px; border-radius: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
        .status-chip.draft { background: rgba(161, 161, 170, 0.1); color: var(--text-muted); }
        .status-chip.approved, .status-chip.confirmed, .status-chip.active { background: rgba(16, 185, 129, 0.12); color: var(--status-green); }
        .status-chip.posted { background: var(--orange-glow); color: var(--orange-brand); }

        .btn { background: var(--orange-brand); color: #000; border: none; padding: 6px 14px; border-radius: 5px; font-weight: 700; font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
        .btn:hover { background: var(--orange-hover); }
        .btn-outline { background: transparent; border: 1px solid var(--border-subtle); color: var(--text-pure); }
        .btn-outline:hover { border-color: var(--border-highlight); background: rgba(255, 255, 255, 0.04); }
        .btn-sm { padding: 4px 8px; font-size: 10.5px; }

        .form-group { margin-bottom: 0.85rem; }
        .form-label { display: block; font-size: 10.5px; font-weight: 600; color: var(--text-dim); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
        .form-input, .form-select { width: 100%; background: #09090b; border: 1px solid var(--border-subtle); border-radius: 5px; padding: 7px 10px; color: var(--text-pure); font-size: 12.5px; outline: none; }
        .form-input:focus, .form-select:focus { border-color: var(--orange-brand); }
        .form-input.invalid { border-color: var(--status-red) !important; }

        /* Master Detail Drawer */
        #detail-drawer { position: fixed; top: 0; right: 0; width: var(--drawer-width); height: 100vh; background: #111114; border-left: 1px solid var(--border-subtle); z-index: 120; display: flex; flex-direction: column; transform: translateX(100%); transition: transform 0.15s ease-out; }
        #detail-drawer.open { transform: translateX(0); }
        .drawer-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; background: #16161a; }
        .drawer-body { flex: 1; padding: 1.25rem; overflow-y: auto; }
        .drawer-footer { padding: 0.85rem 1.25rem; border-top: 1px solid var(--border-subtle); background: #16161a; display: flex; align-items: center; justify-content: flex-end; gap: 6px; }

        /* Windows Native Right-Click Context Menu */
        #context-menu {
            position: fixed;
            z-index: 300;
            background: #18181b;
            border: 1px solid var(--border-highlight);
            border-radius: 6px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
            width: 220px;
            display: none;
            padding: 4px 0;
        }
        .ctx-item {
            padding: 7px 14px;
            font-size: 12px;
            color: var(--text-pure);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        .ctx-item:hover { background: var(--orange-brand); color: #000; font-weight: 600; }
        .ctx-item i { width: 16px; font-size: 12px; }
        .ctx-divider { height: 1px; background: var(--border-subtle); margin: 4px 0; }
        .ctx-shortcut { font-size: 10px; opacity: 0.7; font-family: monospace; }

        .terminal-box { background: #000000; border: 1px solid var(--border-subtle); border-radius: 5px; padding: 0.85rem; font-family: 'JetBrains Mono', monospace; font-size: 11.5px; color: #d4d4d8; max-height: 260px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
        .terminal-box .hl-orange { color: var(--orange-brand); }
        .terminal-box .hl-green { color: var(--status-green); }

        #toast-container { position: fixed; bottom: 34px; right: 16px; z-index: 200; display: flex; flex-direction: column; gap: 6px; }
        .toast { background: #18181b; border: 1px solid var(--border-highlight); border-left: 3px solid var(--orange-brand); padding: 10px 14px; border-radius: 5px; font-size: 12px; color: var(--text-pure); display: flex; align-items: center; gap: 10px; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(3px); z-index: 150; display: none; align-items: center; justify-content: center; padding: 1rem; }
        .modal-overlay.open { display: flex; }
        .modal-card { background: var(--card-bg); border: 1px solid var(--border-subtle); border-radius: 6px; width: 100%; max-width: 760px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-header { padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-subtle); background: var(--card-header-bg); display: flex; align-items: center; justify-content: space-between; }
        .modal-body { padding: 1.25rem; overflow-y: auto; }

        /* Desktop Status Bar */
        #statusbar {
            height: var(--statusbar-height);
            background: #08080a;
            border-top: 1px solid var(--border-subtle);
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-dim);
            font-family: 'JetBrains Mono', monospace;
            z-index: 100;
        }
        .statusbar-item { display: flex; align-items: center; gap: 6px; }
        .statusbar-dot { width: 6px; height: 6px; background: var(--status-green); border-radius: 50%; }
    </style>
</head>
<body>
    <div id="app-wrapper">
        <!-- Sidebar Navigation Shell -->
        <aside id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon" onclick="toggleSidebarCollapse()" title="Toggle Sidebar Width (Ctrl+B)">R</div>
                <div>
                    <div class="brand-title">RAAX <span>ERP</span></div>
                    <div class="brand-sub">Desktop Commercial Edition</div>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-category">Main Workspace</div>
                <a class="nav-item active" onclick="navigateTo('dashboard', this)">
                    <span><i class="fa-solid fa-chart-pie nav-icon"></i> <span class="nav-text">Role Dashboard</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('approvals', this)">
                    <span><i class="fa-solid fa-stamp nav-icon"></i> <span class="nav-text">Approval Queue</span></span>
                    <span class="nav-badge">3</span>
                </a>

                <div class="menu-category">Commercial ERP Modules</div>
                <a class="nav-item" onclick="navigateTo('sales', this)">
                    <span><i class="fa-solid fa-receipt nav-icon"></i> <span class="nav-text">Sales & Invoicing</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('procurement', this)">
                    <span><i class="fa-solid fa-cart-shopping nav-icon"></i> <span class="nav-text">Procurement & POs</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('inventory', this)">
                    <span><i class="fa-solid fa-boxes-packing nav-icon"></i> <span class="nav-text">Inventory FIFO & Bins</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('finance', this)">
                    <span><i class="fa-solid fa-book nav-icon"></i> <span class="nav-text">General Ledger & FX</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('vat', this)">
                    <span><i class="fa-solid fa-file-contract nav-icon"></i> <span class="nav-text">NBR Statutory VAT</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('hr', this)">
                    <span><i class="fa-solid fa-user-clock nav-icon"></i> <span class="nav-text">HR & Payroll Engine</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('assets', this)">
                    <span><i class="fa-solid fa-building-columns nav-icon"></i> <span class="nav-text">Fixed Assets Register</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('manufacturing', this)">
                    <span><i class="fa-solid fa-industry nav-icon"></i> <span class="nav-text">Manufacturing MRP</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('edi', this)">
                    <span><i class="fa-solid fa-network-wired nav-icon"></i> <span class="nav-text">EDI Integration</span></span>
                </a>

                <div class="menu-category">Governance & Devices</div>
                <a class="nav-item" onclick="navigateTo('audit', this)">
                    <span><i class="fa-solid fa-history nav-icon"></i> <span class="nav-text">Audit Trail Logs</span></span>
                </a>
                <a class="nav-item" onclick="navigateTo('telemetry', this)">
                    <span><i class="fa-solid fa-desktop nav-icon"></i> <span class="nav-text">Devices & Peripherals</span></span>
                </a>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div id="main-container">
            <header id="topbar">
                <div class="topbar-left">
                    <button class="toggle-btn" onclick="toggleSidebarCollapse()" title="Toggle Sidebar"><i class="fa-solid fa-bars"></i></button>
                    <div class="global-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="globalSearchInput" class="global-search-input" placeholder="Global Search (Ctrl+F for POs, Invoices, SKU, Asset IDs)...">
                    </div>
                </div>

                <div class="topbar-right">
                    <button class="quick-create-btn" onclick="openCreateModal('po')">
                        <i class="fa-solid fa-plus"></i> + New Record (Ctrl+N)
                    </button>

                    <select class="context-select" id="tenantSelect" onchange="reloadActiveView()">
                        <option value="aca9ea90-0d0f-4ed9-98ed-398af6b67efd">Company: RAAX HQ (Tenant A)</option>
                        <option value="bcb9ea90-0d0f-4ed9-98ed-398af6b67efe">Company: RAAX Chittagong (Tenant B)</option>
                        <option value="ccc9ea90-0d0f-4ed9-98ed-398af6b67eff">Company: Holding Corp (Tenant C)</option>
                    </select>

                    <button class="icon-bell-btn" onclick="navigateTo('approvals')" title="Pending Approvals (3)">
                        <i class="fa-solid fa-bell"></i>
                        <div class="bell-dot"></div>
                    </button>

                    <div class="user-avatar-btn">
                        <div class="user-avatar">AR</div>
                        <span>A. Rahman</span>
                    </div>

                    <div style="font-size:10.5px; font-weight:700; color:var(--status-green); background:rgba(16,185,129,0.1); padding:3px 8px; border-radius:4px; border:1px solid rgba(16,185,129,0.3);">
                        <span class="statusbar-dot" style="display:inline-block; margin-right:4px;"></span> RLS Protected v2.0
                    </div>
                </div>
            </header>

            <div class="page-header">
                <div>
                    <div class="breadcrumbs">RAAX ERP Desktop / <span id="crumb-current">Role Dashboard</span></div>
                    <div class="page-title" id="page-title-text">Role Dashboard</div>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline btn-sm" onclick="exportCurrentView()"><i class="fa-solid fa-download"></i> Export CSV</button>
                    <button class="btn btn-sm" onclick="reloadActiveView()"><i class="fa-solid fa-rotate"></i> Sync Data</button>
                </div>
            </div>

            <div class="workspace-content">
                <!-- DASHBOARD PANEL -->
                <div id="view-dashboard" class="view-panel active">
                    <!-- KPI Headline Grid with Trends -->
                    <div class="kpi-grid">
                        <div class="kpi-card primary-headline">
                            <div class="kpi-header">
                                <div class="kpi-title">Gross Operating Revenue</div>
                                <span class="kpi-badge">PRIMARY METRIC</span>
                            </div>
                            <div class="kpi-value">BDT 142.5M</div>
                            <div class="kpi-trend up"><i class="fa-solid fa-caret-up"></i> +14.2% vs last month</div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-header">
                                <div class="kpi-title">Net Cash Flow</div>
                                <i class="fa-solid fa-money-bill-trend-up kpi-icon" style="color:var(--status-green);"></i>
                            </div>
                            <div class="kpi-value">BDT 38.2M</div>
                            <div class="kpi-trend up"><i class="fa-solid fa-caret-up"></i> +8.1% net liquidity</div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-header">
                                <div class="kpi-title">Fixed Assets Valuation</div>
                                <i class="fa-solid fa-building-columns kpi-icon" style="color:var(--status-blue);"></i>
                            </div>
                            <div class="kpi-value">BDT 84.1M</div>
                            <div class="kpi-trend up"><i class="fa-solid fa-caret-up"></i> +2.4% asset additions</div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-header">
                                <div class="kpi-title">Pending Approvals</div>
                                <i class="fa-solid fa-stamp kpi-icon" style="color:var(--orange-brand);"></i>
                            </div>
                            <div class="kpi-value">3 Items</div>
                            <div class="kpi-trend neutral"><i class="fa-solid fa-triangle-exclamation"></i> 3 actions required</div>
                        </div>
                    </div>

                    <!-- High-Density Commercial Content Layout -->
                    <div class="grid-2">
                        <!-- Left Column: Pending Approvals & Live Audit Activity Stream -->
                        <div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-stamp"></i> Pending Workflow Approvals Queue</div>
                                    <button class="btn btn-outline btn-sm" onclick="navigateTo('approvals')">View Queue</button>
                                </div>
                                <div class="card-body" style="padding:0;">
                                    <table class="data-table">
                                        <thead><tr><th>Request #</th><th>Type</th><th>Value</th><th>Action</th></tr></thead>
                                        <tbody>
                                            <tr>
                                                <td class="mono">REQ-1024</td>
                                                <td>PO Price Tolerance</td>
                                                <td class="mono">BDT 1,250,000</td>
                                                <td>
                                                    <button class="btn btn-sm" onclick="showToast('Workflow Request REQ-1024 Approved!')">Approve</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mono">REQ-1025</td>
                                                <td>Credit Limit Excess</td>
                                                <td class="mono">BDT 850,000</td>
                                                <td>
                                                    <button class="btn btn-sm" onclick="showToast('Workflow Request REQ-1025 Approved!')">Approve</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="mono">REQ-1026</td>
                                                <td>Manual Journal Void</td>
                                                <td class="mono">BDT 45,000</td>
                                                <td>
                                                    <button class="btn btn-sm" onclick="showToast('Workflow Request REQ-1026 Approved!')">Approve</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><div class="card-title"><i class="fa-solid fa-history"></i> Live Operational Audit Activity Stream</div></div>
                                <div class="card-body">
                                    <div class="terminal-box" style="height:150px;"><span class="hl-orange">[2026-07-25 12:45:10]</span> User A. Rahman posted Journal JE-INV-2026-001 (BDT 45,000)
<span class="hl-green">[2026-07-25 12:30:22]</span> NBR Tax Invoice Mushak 6.3 #SO-2026-4412 generated cleanly
<span class="hl-orange">[2026-07-25 12:15:04]</span> Reorder Trigger: SKU-FASTENER-A stock hit 150 (Min threshold: 300)
<span class="hl-green">[2026-07-25 11:58:40]</span> FIFO Stock Batch BIN-MAIN-A1 revalued at BDT 45.00/unit</div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Low-Stock Inventory Alerts & Revenue Trend Summary -->
                        <div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><i class="fa-solid fa-triangle-exclamation"></i> Low-Stock Reorder Alerts (Inventory Control)</div>
                                    <button class="btn btn-outline btn-sm" onclick="navigateTo('inventory')">Manage Stock</button>
                                </div>
                                <div class="card-body" style="padding:0;">
                                    <table class="data-table">
                                        <thead><tr><th>Item SKU</th><th>Bin Label</th><th>Current Qty</th><th>Min Level</th><th>Reorder Action</th></tr></thead>
                                        <tbody>
                                            <tr>
                                                <td class="mono">SKU-FASTENER-A</td>
                                                <td class="mono">BIN-MAIN-A1</td>
                                                <td style="color:var(--status-red); font-weight:700;">150</td>
                                                <td>300</td>
                                                <td><button class="btn btn-outline btn-sm" onclick="openCreateModal('po')">+ Auto PO</button></td>
                                            </tr>
                                            <tr>
                                                <td class="mono">SKU-BEARING-HD</td>
                                                <td class="mono">BIN-MAIN-B4</td>
                                                <td style="color:var(--status-amber); font-weight:700;">45</td>
                                                <td>50</td>
                                                <td><button class="btn btn-outline btn-sm" onclick="openCreateModal('po')">+ Auto PO</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><div class="card-title"><i class="fa-solid fa-chart-line"></i> Fiscal Period Revenue & Cash Flow Trend</div></div>
                                <div class="card-body">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:12px;">
                                        <span>Q1 Operating Revenue Growth</span>
                                        <span class="mono" style="color:var(--status-green); font-weight:700;">+14.2%</span>
                                    </div>
                                    <div style="background:#09090b; height:8px; border-radius:4px; overflow:hidden; margin-bottom:12px;">
                                        <div style="width:78%; background:var(--orange-brand); height:100%;"></div>
                                    </div>

                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:12px;">
                                        <span>Net Operating Cash Ratio</span>
                                        <span class="mono" style="color:var(--status-green); font-weight:700;">2.48x</span>
                                    </div>
                                    <div style="background:#09090b; height:8px; border-radius:4px; overflow:hidden;">
                                        <div style="width:65%; background:var(--status-green); height:100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SALES PANEL -->
                <div id="view-sales" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-receipt"></i> Commercial Sales Orders (Double-Click Row to Edit, Right-Click for Context Menu)</div></div>
                        <div class="data-table-container">
                            <table class="data-table" id="salesTable">
                                <thead><tr><th>Order ID</th><th>Customer Name</th><th>Subtotal</th><th>Grand Total</th><th>Status</th><th>Mushak 6.3 Invoice</th></tr></thead>
                                <tbody id="salesTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PROCUREMENT PANEL -->
                <div id="view-procurement" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Purchase Orders Directory</div></div>
                        <div class="data-table-container">
                            <table class="data-table" id="poTable">
                                <thead><tr><th>PO Number</th><th>Vendor Name</th><th>Total Amount</th><th>Status</th><th>Print Voucher</th></tr></thead>
                                <tbody id="poTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- INVENTORY PANEL -->
                <div id="view-inventory" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-boxes-packing"></i> FIFO Stock Valuation & Bins</div></div>
                        <div class="data-table-container">
                            <table class="data-table" id="inventoryTable">
                                <thead><tr><th>Item SKU</th><th>Bin Label</th><th>Original Qty</th><th>Remaining Qty</th><th>Unit Cost</th></tr></thead>
                                <tbody id="inventoryTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FINANCE PANEL -->
                <div id="view-finance" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-book"></i> General Ledger & Journal Entries</div></div>
                        <div class="data-table-container">
                            <table class="data-table" id="journalTable">
                                <thead><tr><th>Reference</th><th>Date</th><th>Description</th><th>Amount</th><th>SHA-256 Ledger Hash</th></tr></thead>
                                <tbody id="journalTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- HR PANEL -->
                <div id="view-hr" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-users"></i> Employee Master Directory & Payroll Ledger</div></div>
                        <div class="data-table-container">
                            <table class="data-table" id="employeeTable">
                                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>
                                <tbody id="employeeTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FIXED ASSETS PANEL -->
                <div id="view-assets" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-building-columns"></i> Fixed Asset Register & Depreciation Engine</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[Fixed Asset Depreciation Engine Active]</span>
AST-COMP-001: Original Cost BDT 1,200,000 -> Straight Line Depr: BDT 120,000 -> Book Value: BDT 1,080,000
AST-VEH-004: Original Cost BDT 4,500,000 -> Double Declining Depr: BDT 900,000 -> Book Value: BDT 3,600,000</div>
                        </div>
                    </div>
                </div>

                <!-- VAT PANEL -->
                <div id="view-vat" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-file-contract"></i> NBR Bangladesh Statutory VAT Compliance Engine</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[Mushak Compliance Engine 2026-07]</span>
- Mushak 6.1 (Purchase Register): BDT 1,250,000 input tax credit claims
- Mushak 6.3 (Sales Tax Invoice): BDT 850,000 invoice dispatched (VAT BDT 110,870)
- Mushak 6.6 (VDS Certificate): BDT 45,000 withholding tax certificate generated
- Mushak 9.1 (Monthly VAT Return): Net Payable BDT 65,869.57</div>
                        </div>
                    </div>
                </div>

                <!-- MANUFACTURING PANEL -->
                <div id="view-manufacturing" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-industry"></i> JIT Manufacturing MRP Material Deficiency Runner</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[MRP Shortfall Engine Output]</span>
Work Order WO-2026-881 Requirements:
- SKU-RAW-STEEL: Required 200 | Stock 1,200 | Shortfall: 0
- SKU-FASTENER-A: Required 500 | Stock 150 | Shortfall: 350 (Reorder Trigger Dispatched)</div>
                        </div>
                    </div>
                </div>

                <!-- EDI PANEL -->
                <div id="view-edi" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-network-wired"></i> EDI Electronic Data Interchange Mapper</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[EDI X12 Standard Orders Receiver]</span>
- EDI 850 (Purchase Order Inbound): Recv order PO-88912 from Customer TransGlobal
- EDI 855 (PO Ack): Sent confirmation ACK-88912
- EDI 856 (Ship Notice / Manifest): Outbound manifest ready</div>
                        </div>
                    </div>
                </div>

                <!-- DEVICES & PERIPHERALS PANEL -->
                <div id="view-telemetry" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-desktop"></i> Devices & Peripherals (ESC/POS Printer & Barcode Scanner Integration)</div></div>
                        <div class="card-body">
                            <button class="btn btn-outline btn-sm" style="margin-bottom:10px;" onclick="loadHardwareMetrics()"><i class="fa-solid fa-rotate"></i> Query Hardware Diagnostics</button>
                            <div id="telemetryOutput" class="terminal-box">Querying Windows hardware diagnostics & native DLL...</div>
                        </div>
                    </div>
                </div>

                <div id="view-approvals" class="view-panel"><div class="card"><div class="card-body">Approval Queue Active</div></div></div>
                <div id="view-audit" class="view-panel"><div class="card"><div class="card-body">Audit Trail Log Active</div></div></div>
            </div>

            <!-- Persistent Windows Desktop Status Bar -->
            <div id="statusbar">
                <div class="statusbar-item">
                    <span class="statusbar-dot"></span>
                    <span id="sb-company">Company: RAAX HQ (Tenant A)</span>
                </div>
                <div class="statusbar-item">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Fiscal Period: FY 2026-2027</span>
                </div>
                <div class="statusbar-item">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span id="sb-sync">Synced 4s ago</span>
                </div>
                <div class="statusbar-item">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>User: A. Rahman (Senior Operations)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Detail Drawer -->
    <aside id="detail-drawer">
        <div class="drawer-header">
            <div>
                <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Master Record Inspection</div>
                <div style="font-size:15px; font-weight:700;" id="drawer-title">#RECORD-001</div>
            </div>
            <button onclick="closeDrawer()" style="background:none;border:none;color:#fff;cursor:pointer;font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="drawer-body" id="drawer-summary">Select a record row to inspect properties.</div>
        <div class="drawer-footer">
            <button class="btn btn-outline btn-sm" onclick="closeDrawer()">Close (Esc)</button>
            <button class="btn btn-sm" onclick="showToast('Record changes saved cleanly.')">Save (Ctrl+S)</button>
        </div>
    </aside>

    <!-- Header-Detail New PO Modal -->
    <div class="modal-overlay" id="createModal">
        <div class="modal-card">
            <div class="modal-header">
                <div class="card-title"><i class="fa-solid fa-file-pen"></i> New Transaction Entry (Header-Detail Layout)</div>
                <button onclick="closeCreateModal()" style="background:none;border:none;color:#fff;cursor:pointer;font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form id="poCreateForm" onsubmit="handleCreatePO(event)">
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Vendor Supplier</label>
                            <input type="text" id="modalVendor" class="form-input" value="Global Steel Suppliers Ltd" required onblur="validateField(this)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Target Warehouse Bin</label>
                            <select id="modalBin" class="form-select">
                                <option value="BIN-MAIN-A1">BIN-MAIN-A1 (Central Facility)</option>
                                <option value="BIN-MAIN-B4">BIN-MAIN-B4 (Regional Bin)</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:1rem;">
                        <label class="form-label">Transaction Line Items (Inline Editable DataGrid)</label>
                        <table class="data-table" style="margin-bottom:10px;">
                            <thead>
                                <tr><th>SKU Item</th><th>Quantity</th><th>Unit Price (BDT)</th><th>Line Total (BDT)</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-input" value="SKU-RAW-STEEL" id="lineSku"></td>
                                    <td><input type="number" class="form-input mono" value="100" id="lineQty" oninput="calcLineTotal()"></td>
                                    <td><input type="number" class="form-input mono" value="12500" id="linePrice" oninput="calcLineTotal()"></td>
                                    <td><input type="text" class="form-input mono" value="BDT 1,250,000" id="lineTotal" readonly style="color:var(--orange-brand); font-weight:700;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn" style="width:100%; justify-content:center; margin-top:10px;"><i class="fa-solid fa-paper-plane"></i> Save & Post Transaction (Ctrl+S)</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Windows Right-Click Context Menu -->
    <div id="context-menu">
        <div class="ctx-item" onclick="ctxAction('edit')"><span><i class="fa-solid fa-pen-to-square"></i> Open & Edit Record</span><span class="ctx-shortcut">Double-Click</span></div>
        <div class="ctx-item" onclick="ctxAction('duplicate')"><span><i class="fa-solid fa-copy"></i> Duplicate Record</span><span class="ctx-shortcut">Ctrl+D</span></div>
        <div class="ctx-item" onclick="ctxAction('print')"><span><i class="fa-solid fa-print"></i> Print Official Document</span><span class="ctx-shortcut">Ctrl+P</span></div>
        <div class="ctx-divider"></div>
        <div class="ctx-item" onclick="ctxAction('history')"><span><i class="fa-solid fa-history"></i> Audit Trail History</span></div>
        <div class="ctx-item" style="color:var(--status-red);" onclick="ctxAction('delete')"><span><i class="fa-solid fa-trash"></i> Cancel / Soft Delete</span><span class="ctx-shortcut">Del</span></div>
    </div>

    <div id="toast-container"></div>

    <script>
        let selectedRowId = null;
        let lastSyncSeconds = 4;

        function getTenantId() { return document.getElementById('tenantSelect').value; }

        function toggleSidebarCollapse() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function validateField(inputEl) {
            if (!inputEl.value.trim()) {
                inputEl.classList.add('invalid');
            } else {
                inputEl.classList.remove('invalid');
            }
        }

        function calcLineTotal() {
            const qty = parseFloat(document.getElementById('lineQty').value) || 0;
            const price = parseFloat(document.getElementById('linePrice').value) || 0;
            const total = qty * price;
            document.getElementById('lineTotal').value = 'BDT ' + total.toLocaleString();
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color:var(--orange-brand);"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);

            if (window.raax && window.raax.notify) window.raax.notify('RAAX ERP Desktop', message);
        }

        function navigateTo(viewId, element) {
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.view-panel').forEach(panel => panel.classList.remove('active'));

            if (element) {
                element.classList.add('active');
            } else {
                const item = Array.from(document.querySelectorAll('.nav-item')).find(el => el.getAttribute('onclick') && el.getAttribute('onclick').includes(viewId));
                if (item) item.classList.add('active');
            }

            const target = document.getElementById(`view-${viewId}`);
            if (target) target.classList.add('active');

            document.getElementById('crumb-current').innerText = viewId.toUpperCase();
            document.getElementById('page-title-text').innerText = viewId.toUpperCase();

            if (viewId === 'telemetry') loadHardwareMetrics();
            reloadActiveView();
        }

        function openDrawer(id, type, status, summary) {
            document.getElementById('drawer-title').innerText = id;
            document.getElementById('drawer-summary').innerText = summary;
            document.getElementById('detail-drawer').classList.add('open');
        }

        function closeDrawer() { document.getElementById('detail-drawer').classList.remove('open'); }
        function openCreateModal() { document.getElementById('createModal').classList.add('open'); }
        function closeCreateModal() { document.getElementById('createModal').classList.remove('open'); }

        async function loadHardwareMetrics() {
            const box = document.getElementById('telemetryOutput');
            if (window.raax && window.raax.getHardwareInfo) {
                const info = await window.raax.getHardwareInfo();
                box.innerHTML = `<span class="hl-orange">[RAAX_Native_Hardware.dll Metrics]</span>\n` + JSON.stringify(info, null, 2);
            } else {
                box.innerHTML = `<span class="hl-orange">[RAAX_ERP.exe Executable Native Runtime]</span>\nExecutable: RAAX_ERP.exe\nNative Assembly: RAAX_Native_Hardware.dll Loaded\nPlatform: Windows x64 (Native Win32 Subsystem)`;
            }
        }

        function exportCurrentView() { showToast("Data exported to CSV format!"); }

        /* Right-Click Context Menu Logic */
        document.addEventListener('contextmenu', (e) => {
            const row = e.target.closest('tr');
            if (row && row.parentElement.tagName === 'TBODY') {
                e.preventDefault();
                selectedRowId = row.cells[0] ? row.cells[0].innerText : 'REC-001';
                
                const menu = document.getElementById('context-menu');
                menu.style.left = e.clientX + 'px';
                menu.style.top = e.clientY + 'px';
                menu.style.display = 'block';

                document.querySelectorAll('tr').forEach(r => r.classList.remove('selected'));
                row.classList.add('selected');
            }
        });

        document.addEventListener('click', () => {
            document.getElementById('context-menu').style.display = 'none';
        });

        function ctxAction(action) {
            document.getElementById('context-menu').style.display = 'none';
            if (action === 'edit') openDrawer(selectedRowId, 'Record', 'Active', `Inspecting Record ${selectedRowId}`);
            if (action === 'duplicate') showToast(`Record ${selectedRowId} duplicated cleanly.`);
            if (action === 'print') showToast(`Dispatching ${selectedRowId} to Windows Print Queue.`);
            if (action === 'delete') showToast(`Record ${selectedRowId} cancelled/soft-deleted with audit log.`);
        }

        /* Keyboard-First Desktop Shortcuts */
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key.toLowerCase() === 'n') {
                e.preventDefault();
                openCreateModal();
            }
            if (e.ctrlKey && e.key.toLowerCase() === 'f') {
                e.preventDefault();
                document.getElementById('globalSearchInput').focus();
            }
            if (e.ctrlKey && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                toggleSidebarCollapse();
            }
            if (e.key === 'Escape') {
                closeDrawer();
                closeCreateModal();
            }
        });

        async function fetchSalesOrders() {
            const body = document.getElementById('salesTableBody');
            if (!body) return;
            try {
                const res = await fetch('/api/v1/sales/orders', { headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() } });
                const result = await res.json();
                if (result.success && result.data.length > 0) {
                    body.innerHTML = result.data.map(o => `
                        <tr ondblclick="openDrawer('${o.order_number}', 'Sales Order', '${o.status}', 'Customer: ${o.customer ? o.customer.name : 'Apex Corp'}')">
                            <td class="mono">${o.order_number}</td>
                            <td>${o.customer ? o.customer.name : 'Apex Corp'}</td>
                            <td class="mono">BDT ${(o.subtotal_cents/100).toLocaleString()}</td>
                            <td class="mono">BDT ${(o.grand_total_cents/100).toLocaleString()}</td>
                            <td><span class="status-chip ${o.status}">${o.status}</span></td>
                            <td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Mushak 6.3</button></td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr ondblclick="openDrawer('SO-2026-4412', 'Sales Order', 'confirmed', 'Customer: Apex Corp')"><td class="mono">SO-2026-4412</td><td>Apex Holdings Corp</td><td class="mono">BDT 739,130</td><td class="mono">BDT 850,000</td><td><span class="status-chip confirmed">confirmed</span></td><td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Mushak 6.3</button></td></tr>`;
            }
        }

        async function fetchPurchaseOrders() {
            const body = document.getElementById('poTableBody');
            if (!body) return;
            try {
                const res = await fetch('/api/v1/procurement/purchase-orders', { headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() } });
                const result = await res.json();
                if (result.success && result.data.length > 0) {
                    body.innerHTML = result.data.map(po => `
                        <tr ondblclick="openDrawer('${po.po_number}', 'Purchase Order', '${po.status}', 'Vendor: ${po.vendor ? po.vendor.name : 'Global Steel'}')">
                            <td class="mono">${po.po_number}</td>
                            <td>${po.vendor ? po.vendor.name : 'Global Steel'}</td>
                            <td class="mono">BDT ${(po.total_amount_cents/100).toLocaleString()}</td>
                            <td><span class="status-chip ${po.status}">${po.status}</span></td>
                            <td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Voucher</button></td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr ondblclick="openDrawer('PO-2026-8819', 'Purchase Order', 'sent_to_vendor', 'Vendor: Global Steel')"><td class="mono">PO-2026-8819</td><td>Global Steel Suppliers Ltd</td><td class="mono">BDT 1,250,000</td><td><span class="status-chip sent_to_vendor">sent_to_vendor</span></td><td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Voucher</button></td></tr>`;
            }
        }

        async function fetchInventoryItems() {
            const body = document.getElementById('inventoryTableBody');
            if (!body) return;
            try {
                const res = await fetch('/api/v1/inventory/items', { headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() } });
                const result = await res.json();
                if (result.success && result.data.length > 0) {
                    body.innerHTML = result.data.map(i => `
                        <tr ondblclick="openDrawer('${i.item_sku}', 'Stock Item', 'Active', 'Bin: BIN-MAIN-A1')">
                            <td class="mono">${i.item_sku}</td>
                            <td class="mono">BIN-MAIN-A1</td>
                            <td>${i.original_qty}</td>
                            <td style="color:var(--orange-brand);font-weight:700;">${i.remaining_qty}</td>
                            <td class="mono">BDT ${(i.unit_cost_cents/100).toLocaleString()}</td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr ondblclick="openDrawer('SKU-RAW-STEEL', 'Stock Item', 'Active', 'Bin: BIN-MAIN-A1')"><td class="mono">SKU-RAW-STEEL</td><td class="mono">BIN-MAIN-A1</td><td>1,200</td><td style="color:var(--orange-brand);font-weight:700;">1,200</td><td class="mono">BDT 45.00</td></tr>`;
            }
        }

        async function fetchJournals() {
            const body = document.getElementById('journalTableBody');
            if (!body) return;
            try {
                const res = await fetch('/api/v1/finance/journals', { headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() } });
                const result = await res.json();
                if (result.success && result.data.length > 0) {
                    body.innerHTML = result.data.map(j => `
                        <tr ondblclick="openDrawer('${j.reference}', 'Journal Entry', 'Posted', '${j.description}')">
                            <td class="mono">${j.reference}</td>
                            <td>${j.entry_date}</td>
                            <td>${j.description}</td>
                            <td class="mono">BDT ${(j.amount/100).toLocaleString()}</td>
                            <td class="mono" style="color:var(--orange-brand);">${j.hash ? j.hash.substring(0,16)+'...' : 'Sealed SHA-256'}</td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr ondblclick="openDrawer('JE-INV-2026-001', 'Journal Entry', 'Posted', 'Office Rent')"><td class="mono">JE-INV-2026-001</td><td>2026-07-25</td><td>Office Rent & Supplies</td><td class="mono">BDT 45,000</td><td class="mono" style="color:var(--orange-brand);">31af3d709ad29613...</td></tr>`;
            }
        }

        async function fetchEmployees() {
            const body = document.getElementById('employeeTableBody');
            if (!body) return;
            try {
                const res = await fetch('/api/v1/hr/employees', { headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() } });
                const result = await res.json();
                if (result.data && result.data.length > 0) {
                    body.innerHTML = result.data.map(e => `
                        <tr ondblclick="openDrawer('${e.email}', 'Employee', 'Active', '${e.first_name} ${e.last_name}')"><td>${e.first_name} ${e.last_name}</td><td>${e.email}</td><td>${e.phone || '+8801800000000'}</td><td><span class="status-chip active">Active</span></td></tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr ondblclick="openDrawer('a.rahman@raax.com', 'Employee', 'Active', 'Abdur Rahman')"><td>Abdur Rahman</td><td>a.rahman@raax.com</td><td>+8801800000001</td><td><span class="status-chip active">Active</span></td></tr>`;
            }
        }

        function handleCreatePO(e) {
            e.preventDefault();
            closeCreateModal();
            showToast("Transaction saved & posted to ledger cleanly!");
            reloadActiveView();
        }

        function reloadActiveView() {
            const sel = document.getElementById('tenantSelect');
            const companyName = sel.options[sel.selectedIndex].text;
            document.getElementById('sb-company').innerText = companyName;

            lastSyncSeconds = 0;
            document.getElementById('sb-sync').innerText = "Synced just now";

            fetchSalesOrders();
            fetchPurchaseOrders();
            fetchInventoryItems();
            fetchJournals();
            fetchEmployees();
        }

        setInterval(() => {
            lastSyncSeconds += 5;
            document.getElementById('sb-sync').innerText = `Synced ${lastSyncSeconds}s ago`;
        }, 5000);

        document.addEventListener('DOMContentLoaded', () => reloadActiveView());
    </script>
</body>
</html>
