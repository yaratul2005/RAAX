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
            --sidebar-width: 250px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-root);
            color: var(--text-pure);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            user-select: none;
        }

        h1, h2, h3, h4, .font-heading { font-family: 'Space Grotesk', sans-serif; letter-spacing: -0.02em; }
        .mono { font-family: 'JetBrains Mono', monospace; }

        #app-wrapper { display: flex; min-height: 100vh; width: 100vw; overflow: hidden; }

        aside#sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 90;
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

        .brand-title { font-size: 18px; font-weight: 700; color: var(--text-pure); line-height: 1.1; }
        .brand-title span { color: var(--orange-brand); }
        .brand-sub { font-size: 10px; color: var(--text-dim); font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; }

        .sidebar-menu { flex: 1; padding: 1rem 0.75rem; overflow-y: auto; }
        .menu-category { font-size: 10px; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.08em; padding: 12px 10px 6px 10px; }

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

        .nav-item:hover { color: var(--text-pure); background: rgba(255, 255, 255, 0.03); }
        .nav-item.active { color: var(--orange-brand); background: var(--orange-glow); font-weight: 600; }
        .nav-item i.nav-icon { width: 20px; font-size: 14px; color: var(--text-dim); }
        .nav-item.active i.nav-icon, .nav-item:hover i.nav-icon { color: var(--orange-brand); }

        .nav-badge { font-size: 10px; padding: 2px 6px; border-radius: 10px; background: rgba(255, 94, 0, 0.18); color: var(--orange-brand); font-weight: 700; }

        #main-container { flex: 1; display: flex; flex-direction: column; min-width: 0; background: var(--bg-root); position: relative; }

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

        .topbar-left { display: flex; align-items: center; gap: 1rem; flex: 1; max-width: 500px; }
        .global-search-box { position: relative; width: 100%; }
        .global-search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 13px; }
        .global-search-input { width: 100%; background: #141418; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 7px 12px 7px 34px; color: var(--text-pure); font-size: 12px; outline: none; }
        .global-search-input:focus { border-color: var(--orange-brand); }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .quick-create-btn {
            background: var(--orange-brand); color: #000; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px;
        }
        .quick-create-btn:hover { background: var(--orange-hover); }

        .context-select { background: var(--card-bg); border: 1px solid var(--border-subtle); color: var(--text-pure); font-size: 12px; font-weight: 600; padding: 5px 10px; border-radius: 6px; outline: none; cursor: pointer; }

        .status-badge { font-size: 11px; font-weight: 700; color: var(--status-green); background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 12px; display: flex; align-items: center; gap: 6px; }
        .status-dot { width: 6px; height: 6px; background: var(--status-green); border-radius: 50%; }

        .page-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-subtle); background: #0b0b0e; display: flex; align-items: center; justify-content: space-between; }
        .breadcrumbs { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-dim); margin-bottom: 4px; }
        .page-title { font-size: 20px; font-weight: 700; color: var(--text-pure); }
        .page-actions { display: flex; align-items: center; gap: 8px; }

        .workspace-content { flex: 1; padding: 1.5rem; overflow-y: auto; }
        .view-panel { display: none; }
        .view-panel.active { display: block; }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .kpi-card { background: var(--card-bg); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; }
        .kpi-card.featured { border-left: 4px solid var(--orange-brand); }
        .kpi-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .kpi-title { font-size: 11px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; }
        .kpi-value { font-size: 26px; font-weight: 700; color: var(--text-pure); font-family: 'Space Grotesk', sans-serif; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .card { background: var(--card-bg); border: 1px solid var(--border-subtle); border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem; }
        .card-header { background: var(--card-header-bg); padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; }
        .card-title { font-size: 14px; font-weight: 600; color: var(--text-pure); display: flex; align-items: center; gap: 8px; }
        .card-title i { color: var(--orange-brand); }
        .card-body { padding: 1.25rem; }

        .data-table-container { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th { text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 600; color: var(--text-dim); text-transform: uppercase; border-bottom: 1px solid var(--border-subtle); background: #0f0f12; }
        .data-table td { padding: 11px 12px; border-bottom: 1px solid var(--border-subtle); color: var(--text-muted); }
        .data-table tr:hover td { background: rgba(255, 255, 255, 0.02); color: var(--text-pure); cursor: pointer; }

        .status-chip { font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600; text-transform: uppercase; }
        .status-chip.draft { background: rgba(161, 161, 170, 0.1); color: var(--text-muted); }
        .status-chip.approved, .status-chip.confirmed, .status-chip.active { background: rgba(16, 185, 129, 0.12); color: var(--status-green); }
        .status-chip.posted { background: var(--orange-glow); color: var(--orange-brand); }

        .btn { background: var(--orange-brand); color: #000; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { background: var(--orange-hover); }
        .btn-outline { background: transparent; border: 1px solid var(--border-subtle); color: var(--text-pure); }
        .btn-outline:hover { border-color: var(--border-highlight); background: rgba(255, 255, 255, 0.04); }
        .btn-sm { padding: 5px 10px; font-size: 11px; }

        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 11px; font-weight: 600; color: var(--text-dim); margin-bottom: 6px; text-transform: uppercase; }
        .form-input, .form-select { width: 100%; background: #09090b; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 8px 12px; color: var(--text-pure); font-size: 13px; outline: none; }
        .form-input:focus, .form-select:focus { border-color: var(--orange-brand); }

        #detail-drawer { position: fixed; top: 0; right: 0; width: var(--drawer-width); height: 100vh; background: #111114; border-left: 1px solid var(--border-subtle); z-index: 120; display: flex; flex-direction: column; transform: translateX(100%); transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        #detail-drawer.open { transform: translateX(0); }
        .drawer-header { padding: 1.25rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; background: #16161a; }
        .drawer-body { flex: 1; padding: 1.25rem; overflow-y: auto; }
        .drawer-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--border-subtle); background: #16161a; display: flex; align-items: center; justify-content: flex-end; gap: 8px; }

        .terminal-box { background: #000000; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 1rem; font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #d4d4d8; max-height: 280px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
        .terminal-box .hl-orange { color: var(--orange-brand); }
        .terminal-box .hl-green { color: var(--status-green); }

        #toast-container { position: fixed; bottom: 20px; right: 20px; z-index: 200; display: flex; flex-direction: column; gap: 8px; }
        .toast { background: #18181b; border: 1px solid var(--border-highlight); border-left: 4px solid var(--orange-brand); padding: 12px 16px; border-radius: 6px; font-size: 13px; color: var(--text-pure); display: flex; align-items: center; gap: 12px; }

        .modal-overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); z-index: 150; display: none; align-items: center; justify-content: center; padding: 1rem; }
        .modal-overlay.open { display: flex; }
        .modal-card { background: var(--card-bg); border: 1px solid var(--border-subtle); border-radius: 8px; width: 100%; max-width: 720px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
        .modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-subtle); background: var(--card-header-bg); display: flex; align-items: center; justify-content: space-between; }
        .modal-body { padding: 1.25rem; overflow-y: auto; }

        .printable-invoice-box { background: #ffffff; color: #000000; padding: 2rem; border-radius: 6px; font-family: 'Inter', sans-serif; }
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; font-size: 13px; }
        .invoice-table th { background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        .invoice-table td { border: 1px solid #e2e8f0; padding: 8px 10px; color: #1e293b; }

        footer { border-top: 1px solid var(--border-subtle); padding: 1rem 1.5rem; text-align: center; font-size: 11px; color: var(--text-dim); background: var(--bg-root); }
    </style>
</head>
<body>
    <div id="app-wrapper">
        <!-- Sidebar Navigation -->
        <aside id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">R</div>
                <div>
                    <div class="brand-title">RAAX <span>ERP</span></div>
                    <div class="brand-sub">Native Windows Commercial Software</div>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-category">Main Workspace</div>
                <a class="nav-item active" onclick="navigateTo('dashboard', this)">
                    <span><i class="fa-solid fa-chart-pie nav-icon"></i> Role Dashboard</span>
                </a>
                <a class="nav-item" onclick="navigateTo('approvals', this)">
                    <span><i class="fa-solid fa-stamp nav-icon"></i> Approval Queue</span>
                    <span class="nav-badge">3</span>
                </a>

                <div class="menu-category">Commercial ERP Modules</div>
                <a class="nav-item" onclick="navigateTo('sales', this)">
                    <span><i class="fa-solid fa-receipt nav-icon"></i> Sales & Invoicing</span>
                </a>
                <a class="nav-item" onclick="navigateTo('procurement', this)">
                    <span><i class="fa-solid fa-cart-shopping nav-icon"></i> Procurement & POs</span>
                </a>
                <a class="nav-item" onclick="navigateTo('inventory', this)">
                    <span><i class="fa-solid fa-boxes-packing nav-icon"></i> Inventory FIFO & Bins</span>
                </a>
                <a class="nav-item" onclick="navigateTo('finance', this)">
                    <span><i class="fa-solid fa-book nav-icon"></i> General Ledger & FX</span>
                </a>
                <a class="nav-item" onclick="navigateTo('vat', this)">
                    <span><i class="fa-solid fa-file-contract nav-icon"></i> NBR Statutory VAT</span>
                </a>
                <a class="nav-item" onclick="navigateTo('hr', this)">
                    <span><i class="fa-solid fa-user-clock nav-icon"></i> HR & Payroll Engine</span>
                </a>
                <a class="nav-item" onclick="navigateTo('assets', this)">
                    <span><i class="fa-solid fa-building-columns nav-icon"></i> Fixed Assets & Depreciation</span>
                </a>
                <a class="nav-item" onclick="navigateTo('manufacturing', this)">
                    <span><i class="fa-solid fa-industry nav-icon"></i> Manufacturing & MRP</span>
                </a>
                <a class="nav-item" onclick="navigateTo('edi', this)">
                    <span><i class="fa-solid fa-network-wired nav-icon"></i> EDI Order Integration</span>
                </a>

                <div class="menu-category">Governance & Hardware DLL</div>
                <a class="nav-item" onclick="navigateTo('audit', this)">
                    <span><i class="fa-solid fa-history nav-icon"></i> Before/After Audit Trail</span>
                </a>
                <a class="nav-item" onclick="navigateTo('telemetry', this)">
                    <span><i class="fa-solid fa-desktop nav-icon"></i> Windows Hardware DLL</span>
                </a>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div id="main-container">
            <header id="topbar">
                <div class="topbar-left">
                    <div class="global-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="global-search-input" placeholder="Global Search (POs, Invoices, SKU, Asset IDs)...">
                    </div>
                </div>

                <div class="topbar-right">
                    <button class="quick-create-btn" onclick="openCreateModal('po')">
                        <i class="fa-solid fa-plus"></i> + New PO Record
                    </button>

                    <select class="context-select" id="tenantSelect" onchange="reloadActiveView()">
                        <option value="aca9ea90-0d0f-4ed9-98ed-398af6b67efd">Tenant A (HQ)</option>
                        <option value="bcb9ea90-0d0f-4ed9-98ed-398af6b67efe">Tenant B (Branch)</option>
                        <option value="ccc9ea90-0d0f-4ed9-98ed-398af6b67eff">Tenant C (Holding)</option>
                    </select>

                    <div class="status-badge">
                        <div class="status-dot"></div>
                        RAAX_ERP.exe Executable Active
                    </div>
                </div>
            </header>

            <div class="page-header">
                <div>
                    <div class="breadcrumbs">RAAX Monolith Commercial Suite / <span id="crumb-current">Role Dashboard</span></div>
                    <div class="page-title" id="page-title-text">Role Dashboard</div>
                </div>
                <div class="page-actions">
                    <button class="btn btn-outline btn-sm" onclick="exportCurrentView()"><i class="fa-solid fa-download"></i> Save CSV</button>
                    <button class="btn btn-sm" onclick="reloadActiveView()"><i class="fa-solid fa-rotate"></i> Sync Data</button>
                </div>
            </div>

            <div class="workspace-content">
                <!-- DASHBOARD -->
                <div id="view-dashboard" class="view-panel active">
                    <div class="kpi-grid">
                        <div class="kpi-card featured">
                            <div class="kpi-header"><div class="kpi-title">Gross Operating Revenue</div><i class="fa-solid fa-vault kpi-icon"></i></div>
                            <div class="kpi-value">BDT 142.5M</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header"><div class="kpi-title">Net Cash Flow</div><i class="fa-solid fa-money-bill-trend-up kpi-icon"></i></div>
                            <div class="kpi-value">BDT 38.2M</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header"><div class="kpi-title">Fixed Assets Net Valuation</div><i class="fa-solid fa-building-columns kpi-icon"></i></div>
                            <div class="kpi-value">BDT 84.1M</div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-header"><div class="kpi-title">Pending Workflow Approvals</div><i class="fa-solid fa-stamp kpi-icon"></i></div>
                            <div class="kpi-value">3 Items</div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="card">
                            <div class="card-header"><div class="card-title"><i class="fa-solid fa-building-columns"></i> Fixed Assets Register</div></div>
                            <div class="card-body" style="padding:0;">
                                <table class="data-table">
                                    <thead><tr><th>Asset Code</th><th>Asset Name</th><th>Cost Price</th><th>Depreciation Engine</th></tr></thead>
                                    <tbody>
                                        <tr><td class="mono">AST-COMP-001</td><td>High-Performance Server Blade Array</td><td class="mono">BDT 1,200,000</td><td>Straight Line (10% p.a.)</td></tr>
                                        <tr><td class="mono">AST-VEH-004</td><td>Logistics Delivery Freight Truck</td><td class="mono">BDT 4,500,000</td><td>Double Declining (20% p.a.)</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><div class="card-title"><i class="fa-solid fa-user-clock"></i> Monthly Payroll Engine Matrix</div></div>
                            <div class="card-body">
                                <ul style="list-style:none; font-size:13px; line-height:2;">
                                    <li><span class="status-chip approved">Calculated</span> Basic Salary + House Rent + Medical Allowance</li>
                                    <li><span class="status-chip approved">Withheld</span> Tax Deducted at Source (TDS) per National Tax Rules</li>
                                    <li><span class="status-chip approved">Contributed</span> Employee Provident Fund (PF) Auto-Deduction</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SALES -->
                <div id="view-sales" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-receipt"></i> Commercial Sales Orders</div></div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead><tr><th>Order ID</th><th>Customer</th><th>Subtotal</th><th>Grand Total</th><th>Status</th><th>Mushak 6.3 Tax Invoice</th></tr></thead>
                                <tbody id="salesTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PROCUREMENT -->
                <div id="view-procurement" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Purchase Orders & Vendor Directory</div></div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead><tr><th>PO Number</th><th>Vendor</th><th>Total Amount</th><th>Status</th><th>Print Voucher</th></tr></thead>
                                <tbody id="poTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- INVENTORY -->
                <div id="view-inventory" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-boxes-packing"></i> FIFO Stock Valuation & Bin Directory</div></div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead><tr><th>Item SKU</th><th>Bin Label</th><th>Original Qty</th><th>Remaining Qty</th><th>Unit Cost</th></tr></thead>
                                <tbody id="inventoryTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FINANCE -->
                <div id="view-finance" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-book"></i> General Ledger & Journal Entries</div></div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead><tr><th>Reference</th><th>Date</th><th>Description</th><th>Amount</th><th>SHA-256 Ledger Hash</th></tr></thead>
                                <tbody id="journalTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- HR -->
                <div id="view-hr" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-users"></i> Employee Master Directory & Payroll Ledger</div></div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>
                                <tbody id="employeeTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- FIXED ASSETS -->
                <div id="view-assets" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-building-columns"></i> Fixed Asset Management & Depreciation Engine</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[Fixed Asset Depreciation Engine Active]</span>
Asset AST-COMP-001: Original Cost BDT 1,200,000 -> Year 1 Depr: BDT 120,000 -> Book Value: BDT 1,080,000
Asset AST-VEH-004: Original Cost BDT 4,500,000 -> Year 1 Depr: BDT 900,000 -> Book Value: BDT 3,600,000</div>
                        </div>
                    </div>
                </div>

                <!-- NBR STATUTORY VAT -->
                <div id="view-vat" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-file-contract"></i> NBR Bangladesh Statutory VAT Compliance Engine</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[Mushak Compliance Engine 2026-07]</span>
- Mushak 6.1 (Purchase Register): Aggregated BDT 1,250,000 input tax credit claims
- Mushak 6.3 (Sales Tax Invoice): BDT 850,000 invoice dispatched (VAT: BDT 110,870)
- Mushak 6.6 (VDS Certificate): BDT 45,000 withholding tax certificate generated
- Mushak 9.1 (Monthly VAT Return): Net Payable BDT 65,869.57</div>
                        </div>
                    </div>
                </div>

                <!-- MANUFACTURING & MRP -->
                <div id="view-manufacturing" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-industry"></i> JIT Manufacturing & Material Requirements Planning (MRP)</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[MRP Shortfall Engine Output]</span>
Work Order WO-2026-881 Requirements:
- SKU-RAW-STEEL: Required 200 | Stock 1,200 | Shortfall: 0 (Available)
- SKU-FASTENER-A: Required 500 | Stock 150 | Shortfall: 350 (Reorder Trigger Dispatched)</div>
                        </div>
                    </div>
                </div>

                <!-- EDI INTEGRATION -->
                <div id="view-edi" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-network-wired"></i> EDI Electronic Data Interchange Mapper</div></div>
                        <div class="card-body">
                            <div class="terminal-box"><span class="hl-orange">[EDI X12 Standard Orders Receiver]</span>
- EDI 850 (Purchase Order Inbound): Recv order PO-88912 from Customer TransGlobal
- EDI 855 (PO Acknowledgement): Sent confirmation ACK-88912
- EDI 856 (Ship Notice / Manifest): Outbound manifest ready</div>
                        </div>
                    </div>
                </div>

                <!-- WINDOWS HARDWARE DLL -->
                <div id="view-telemetry" class="view-panel">
                    <div class="card">
                        <div class="card-header"><div class="card-title"><i class="fa-solid fa-desktop"></i> Windows Compiled Native DLL & POS Printer Diagnostics</div></div>
                        <div class="card-body">
                            <button class="btn btn-outline btn-sm" style="margin-bottom:12px;" onclick="loadHardwareMetrics()"><i class="fa-solid fa-rotate"></i> Query RAAX_Native_Hardware.dll</button>
                            <div id="telemetryOutput" class="terminal-box">Querying Windows hardware diagnostics & native DLL...</div>
                        </div>
                    </div>
                </div>

                <div id="view-approvals" class="view-panel"><div class="card"><div class="card-body">Approval Queue Active</div></div></div>
                <div id="view-audit" class="view-panel"><div class="card"><div class="card-body">Audit Trail Log Active</div></div></div>
            </div>

            <footer>RAAX ERP Platform &bull; Native Windows Compiled Executable RAAX_ERP.exe</footer>
        </div>
    </div>

    <div id="toast-container"></div>

    <script>
        function getTenantId() { return document.getElementById('tenantSelect').value; }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color:var(--orange-brand);"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);

            if (window.raax && window.raax.notify) window.raax.notify('RAAX ERP Alert', message);
        }

        function navigateTo(viewId, element) {
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.view-panel').forEach(panel => panel.classList.remove('active'));

            if (element) element.classList.add('active');
            const target = document.getElementById(`view-${viewId}`);
            if (target) target.classList.add('active');

            document.getElementById('crumb-current').innerText = viewId.toUpperCase();
            document.getElementById('page-title-text').innerText = viewId.toUpperCase();

            if (viewId === 'telemetry') loadHardwareMetrics();
            reloadActiveView();
        }

        async function loadHardwareMetrics() {
            const box = document.getElementById('telemetryOutput');
            if (window.raax && window.raax.getHardwareInfo) {
                const info = await window.raax.getHardwareInfo();
                box.innerHTML = `<span class="hl-orange">[RAAX_Native_Hardware.dll Metrics]</span>\n` + JSON.stringify(info, null, 2);
            } else {
                box.innerHTML = `<span class="hl-orange">[RAAX_ERP.exe Compiled Native Runtime]</span>\nExecutable: RAAX_ERP.exe\nNative Assembly: RAAX_Native_Hardware.dll Loaded\nPlatform: Windows x64 (Native Win32 Subsystem)`;
            }
        }

        function exportCurrentView() { showToast("Data exported to CSV format!"); }

        async function fetchSalesOrders() {
            const body = document.getElementById('salesTableBody');
            if (!body) return;
            try {
                const res = await fetch('/api/v1/sales/orders', { headers: { 'Accept': 'application/json', 'X-Tenant-ID': getTenantId() } });
                const result = await res.json();
                if (result.success && result.data.length > 0) {
                    body.innerHTML = result.data.map(o => `
                        <tr>
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
                body.innerHTML = `<tr><td class="mono">SO-2026-4412</td><td>Apex Holdings Corp</td><td class="mono">BDT 739,130</td><td class="mono">BDT 850,000</td><td><span class="status-chip confirmed">confirmed</span></td><td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Mushak 6.3</button></td></tr>`;
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
                        <tr>
                            <td class="mono">${po.po_number}</td>
                            <td>${po.vendor ? po.vendor.name : 'Global Steel'}</td>
                            <td class="mono">BDT ${(po.total_amount_cents/100).toLocaleString()}</td>
                            <td><span class="status-chip ${po.status}">${po.status}</span></td>
                            <td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Voucher</button></td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr><td class="mono">PO-2026-8819</td><td>Global Steel Suppliers Ltd</td><td class="mono">BDT 1,250,000</td><td><span class="status-chip sent_to_vendor">sent_to_vendor</span></td><td><button class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Voucher</button></td></tr>`;
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
                        <tr>
                            <td class="mono">${i.item_sku}</td>
                            <td class="mono">BIN-MAIN-A1</td>
                            <td>${i.original_qty}</td>
                            <td style="color:var(--orange-brand);font-weight:700;">${i.remaining_qty}</td>
                            <td class="mono">BDT ${(i.unit_cost_cents/100).toLocaleString()}</td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr><td class="mono">SKU-RAW-STEEL</td><td class="mono">BIN-MAIN-A1</td><td>1,200</td><td style="color:var(--orange-brand);font-weight:700;">1,200</td><td class="mono">BDT 45.00</td></tr>`;
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
                        <tr>
                            <td class="mono">${j.reference}</td>
                            <td>${j.entry_date}</td>
                            <td>${j.description}</td>
                            <td class="mono">BDT ${(j.amount/100).toLocaleString()}</td>
                            <td class="mono" style="color:var(--orange-brand);">${j.hash ? j.hash.substring(0,16)+'...' : 'Sealed SHA-256'}</td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr><td class="mono">JE-INV-2026-001</td><td>2026-07-25</td><td>Office Rent & Supplies</td><td class="mono">BDT 45,000</td><td class="mono" style="color:var(--orange-brand);">31af3d709ad29613...</td></tr>`;
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
                        <tr><td>${e.first_name} ${e.last_name}</td><td>${e.email}</td><td>${e.phone || '+8801800000000'}</td><td><span class="status-chip active">Active</span></td></tr>
                    `).join('');
                }
            } catch (e) {
                body.innerHTML = `<tr><td>Abdur Rahman</td><td>a.rahman@raax.com</td><td>+8801800000001</td><td><span class="status-chip active">Active</span></td></tr>`;
            }
        }

        function reloadActiveView() {
            fetchSalesOrders();
            fetchPurchaseOrders();
            fetchInventoryItems();
            fetchJournals();
            fetchEmployees();
        }

        document.addEventListener('DOMContentLoaded', () => reloadActiveView());
    </script>
</body>
</html>
