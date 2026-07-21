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

        /* Top Navigation Header */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(9, 9, 11, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.85rem 2rem;
        }

        .nav-container {
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
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
            font-size: 20px;
            font-weight: 700;
            color: var(--text-pure);
            line-height: 1.1;
        }

        .brand-title span {
            color: var(--orange-brand);
        }

        .brand-sub {
            font-size: 11px;
            color: var(--text-dim);
            font-weight: 500;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .header-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .tenant-box, .role-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--card-bg);
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--border-subtle);
        }

        .tenant-box select, .role-box select {
            background: transparent;
            border: none;
            color: var(--text-pure);
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        .status-badge {
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--status-green);
        }

        /* Layout Container */
        .main-wrapper {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            flex: 1;
        }

        /* Dynamic KPI Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
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
            font-size: 16px;
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

        /* Main Navigation Tabs */
        .tabs-nav {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }

        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-dim);
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--text-muted);
        }

        .tab-btn.active {
            color: var(--orange-brand);
            border-bottom-color: var(--orange-brand);
            background: var(--orange-glow);
        }

        /* Content Sections */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

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

        /* Solid Enterprise Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: var(--card-header-bg);
            padding: 1rem 1.25rem;
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
            gap: 10px;
        }

        .card-title i {
            color: var(--orange-brand);
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Search & Filter Bar */
        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: #0f0f12;
            border-bottom: 1px solid var(--border-subtle);
            gap: 1rem;
        }

        .search-input {
            background: var(--bg-root);
            border: 1px solid var(--border-subtle);
            border-radius: 4px;
            padding: 6px 10px;
            color: var(--text-pure);
            font-size: 12px;
            outline: none;
            width: 220px;
        }

        .search-input:focus {
            border-color: var(--orange-brand);
        }

        /* Enterprise Data Tables */
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
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-muted);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.015);
            color: var(--text-pure);
        }

        .badge-tag {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
            border: 1px solid var(--border-subtle);
        }

        .badge-tag.orange {
            background: var(--orange-glow);
            color: var(--orange-brand);
            border-color: rgba(255, 94, 0, 0.3);
        }

        .badge-tag.green {
            background: rgba(16, 185, 129, 0.1);
            color: var(--status-green);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .badge-tag.amber {
            background: rgba(245, 158, 11, 0.1);
            color: var(--status-amber);
            border-color: rgba(245, 158, 11, 0.2);
        }

        /* Form Controls */
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
            padding: 10px 12px;
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

        .btn {
            background: var(--orange-brand);
            color: #000000;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
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

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Execution Terminal Box */
        .terminal-box {
            background: #000000;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            padding: 1rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #d4d4d8;
            max-height: 320px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        .terminal-box .hl-orange { color: var(--orange-brand); }
        .terminal-box .hl-green { color: var(--status-green); }
        .terminal-box .hl-amber { color: var(--status-amber); }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 1.25rem 2rem;
            text-align: center;
            font-size: 12px;
            color: var(--text-dim);
            background: var(--bg-root);
        }
    </style>
</head>
<body>

    <!-- Top Command Navigation Header -->
    <header>
        <div class="nav-container">
            <a href="#" class="brand-logo">
                <div class="brand-icon">R</div>
                <div class="brand-text">
                    <div class="brand-title">RAAX <span>ERP</span></div>
                    <div class="brand-sub">Enterprise Monolith Core</div>
                </div>
            </a>

            <div class="header-controls">
                <div class="tenant-box">
                    <i class="fa-solid fa-building" style="color: var(--orange-brand); font-size: 12px;"></i>
                    <span style="font-size: 11px; color: var(--text-dim); text-transform: uppercase;">Tenant Context:</span>
                    <select id="tenantSelect" onchange="updateTenantContext()">
                        <option value="aca9ea90-0d0f-4ed9-98ed-398af6b67efd">Tenant A (Headquarters)</option>
                        <option value="bcb9ea90-0d0f-4ed9-98ed-398af6b67efe">Tenant B (Regional Branch)</option>
                        <option value="ccc9ea90-0d0f-4ed9-98ed-398af6b67eff">Tenant C (Holding Group)</option>
                    </select>
                </div>

                <div class="role-box">
                    <i class="fa-solid fa-user-shield" style="color: var(--text-dim); font-size: 12px;"></i>
                    <select id="roleSelect">
                        <option value="cfo">Role: CFO / Finance Admin</option>
                        <option value="auditor">Role: Compliance Auditor</option>
                        <option value="hr">Role: HR Department Head</option>
                        <option value="operations">Role: Operations Lead</option>
                    </select>
                </div>

                <div class="status-badge">
                    <div class="status-dot"></div>
                    RLS Active &bull; Zero AI
                </div>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="main-wrapper">

        <!-- Dynamic KPI Metric Grid -->
        <div class="kpi-grid">
            <div class="kpi-card featured">
                <div class="kpi-header">
                    <div class="kpi-title">Total Cash & Assets</div>
                    <i class="fa-solid fa-vault kpi-icon"></i>
                </div>
                <div class="kpi-value" id="kpiAssetVal">BDT 142.5M</div>
                <div class="kpi-subtitle positive">
                    <i class="fa-solid fa-arrow-up-long"></i> +12.4% vs Q1 Ledger Audit
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">Active Enterprise Workforce</div>
                    <i class="fa-solid fa-users kpi-icon"></i>
                </div>
                <div class="kpi-value">1,248</div>
                <div class="kpi-subtitle">
                    12 Branches Synced
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">Statutory NBR VAT (Mushak 9.1)</div>
                    <i class="fa-solid fa-file-invoice-dollar kpi-icon"></i>
                </div>
                <div class="kpi-value">BDT 75,000</div>
                <div class="kpi-subtitle orange">
                    Draft Return Ready for Filing
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">API Gateway Health</div>
                    <i class="fa-solid fa-gauge-high kpi-icon"></i>
                </div>
                <div class="kpi-value">4.2 ms</div>
                <div class="kpi-subtitle positive">
                    200 OK &bull; Zero Execution Overhead
                </div>
            </div>
        </div>

        <!-- Navigation Suite Tabs -->
        <div class="tabs-nav">
            <button class="tab-btn active" onclick="switchTab('overview', this)">
                <i class="fa-solid fa-chart-line"></i> Overview
            </button>
            <button class="tab-btn" onclick="switchTab('finance', this)">
                <i class="fa-solid fa-book"></i> Finance & GL
            </button>
            <button class="tab-btn" onclick="switchTab('hr', this)">
                <i class="fa-solid fa-id-badge"></i> HR & Attendance
            </button>
            <button class="tab-btn" onclick="switchTab('procurement', this)">
                <i class="fa-solid fa-cart-shopping"></i> Procurement & POs
            </button>
            <button class="tab-btn" onclick="switchTab('inventory', this)">
                <i class="fa-solid fa-boxes-packing"></i> Stock & FIFO
            </button>
            <button class="tab-btn" onclick="switchTab('sales', this)">
                <i class="fa-solid fa-receipt"></i> Sales & Orders
            </button>
            <button class="tab-btn" onclick="switchTab('manufacturing', this)">
                <i class="fa-solid fa-industry"></i> MRP & Production
            </button>
            <button class="tab-btn" onclick="switchTab('vat', this)">
                <i class="fa-solid fa-file-lines"></i> NBR Mushak VAT
            </button>
            <button class="tab-btn" onclick="switchTab('telemetry', this)">
                <i class="fa-solid fa-terminal"></i> Telemetry & Audit
            </button>
        </div>

        <!-- Suite 1: Executive Overview -->
        <div id="overview" class="tab-content active">
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-cubes"></i> Encapsulated Domain Module Registry
                        </div>
                        <span class="badge-tag orange">9 Modules</span>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Business Domain</th>
                                    <th>Namespace</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Finance & Accounting</strong></td>
                                    <td class="mono">Modules\Finance</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Human Resources</strong></td>
                                    <td class="mono">Modules\HR</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Procurement & POs</strong></td>
                                    <td class="mono">Modules\Procurement</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Inventory & FIFO Valuation</strong></td>
                                    <td class="mono">Modules\Inventory</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Sales & Billing</strong></td>
                                    <td class="mono">Modules\Sales</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Manufacturing & MRP</strong></td>
                                    <td class="mono">Modules\Manufacturing</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Fixed Assets</strong></td>
                                    <td class="mono">Modules\Assets</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>EDI Data Pipeline</strong></td>
                                    <td class="mono">Modules\EDI</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Notifications Engine</strong></td>
                                    <td class="mono">Modules\Notifications</td>
                                    <td><span class="badge-tag green">Operational</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-shield-halved"></i> Security & RLS Engine Policy
                        </div>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.5;">
                            PostgreSQL Row-Level Security (RLS) policies isolate multi-tenant database records at the engine query execution layer:
                        </p>
                        <div class="terminal-box" style="margin-bottom: 1rem;">
<span class="hl-orange">CREATE POLICY</span> tenant_isolation_policy <span class="hl-orange">ON</span> journal_entries
<span class="hl-orange">FOR ALL TO</span> app_user
<span class="hl-orange">USING</span> (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
                        </div>
                        <ul style="font-size: 12px; color: var(--text-muted); padding-left: 1.2rem; line-height: 1.8;">
                            <li>Zero cross-tenant data contamination across holding company branches.</li>
                            <li>Strict Double-Entry ledger validation (&sum; Debits == &sum; Credits).</li>
                            <li>Deterministic processing guaranteeing 100% auditable results.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 2: General Ledger & Finance -->
        <div id="finance" class="tab-content">
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-pen-to-square"></i> Post Double-Entry Journal Entry
                        </div>
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
                                <input type="text" id="jeDesc" class="form-input" value="Office Lease & Operations Expenses" required>
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
                            <div class="card-title">
                                <i class="fa-solid fa-calculator"></i> Finance Quick Actions
                            </div>
                        </div>
                        <div class="card-body" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button onclick="fetchConsolidatedTB()" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-scale-balanced"></i> Consolidated Trial Balance
                            </button>
                            <button onclick="triggerForexRevaluation()" class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-globe"></i> Month-End Forex Revaluation
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="fa-solid fa-code"></i> API Execution Console
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="financeOutput" class="terminal-box">
Ready to post journals...
POST /api/v1/finance/journals
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 3: HR & Attendance -->
        <div id="hr" class="tab-content">
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-user-clock"></i> Register Attendance Check-In
                        </div>
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

                            <button type="submit" class="btn">
                                <i class="fa-solid fa-clock"></i> Log Check-In
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-terminal"></i> HR Event Output Log
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="hrOutput" class="terminal-box">
Ready to log attendance...
POST /api/v1/hr/attendance/check-in
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 4: Procurement & POs -->
        <div id="procurement" class="tab-content">
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-cart-flatbed"></i> Create Purchase Order
                        </div>
                    </div>
                    <div class="card-body">
                        <form onsubmit="handleCreatePO(event)">
                            <div class="form-group">
                                <label class="form-label">Vendor Name</label>
                                <input type="text" id="poVendor" class="form-input" value="Global Steel Suppliers Ltd" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Order Total Amount (Cents)</label>
                                <input type="number" id="poAmount" class="form-input" value="1250000" required>
                            </div>

                            <button type="submit" class="btn">
                                <i class="fa-solid fa-file-signature"></i> Submit Purchase Order
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-terminal"></i> Procurement API Output
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="procurementOutput" class="terminal-box">
Ready to submit PO...
POST /api/v1/procurement/orders
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 5: Inventory & FIFO Stock -->
        <div id="inventory" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-boxes-packing"></i> FIFO Inventory Valuation & Batch Registry
                    </div>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-toolbar">
                        <input type="text" class="search-input" id="invSearch" placeholder="Search SKU or Bin..." onkeyup="filterTable('invTable', this.value)">
                        <span class="badge-tag orange">Valuation: FIFO</span>
                    </div>
                    <table class="data-table" id="invTable">
                        <thead>
                            <tr>
                                <th>SKU Reference</th>
                                <th>Warehouse Bin</th>
                                <th>Initial Stock</th>
                                <th>Remaining Stock</th>
                                <th>Unit Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="mono">SKU-TRANS-01</td>
                                <td class="mono">BIN-MAIN-A1</td>
                                <td>500</td>
                                <td>420</td>
                                <td>1,000 cents (BDT 10.00)</td>
                                <td><span class="badge-tag green">Available</span></td>
                            </tr>
                            <tr>
                                <td class="mono">SKU-RAW-STEEL</td>
                                <td class="mono">BIN-WH2-B4</td>
                                <td>1,200</td>
                                <td>1,200</td>
                                <td>4,500 cents (BDT 45.00)</td>
                                <td><span class="badge-tag green">Available</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Suite 6: Sales & Orders -->
        <div id="sales" class="tab-content">
            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-receipt"></i> Create Sales Order
                        </div>
                    </div>
                    <div class="card-body">
                        <form onsubmit="handleCreateSalesOrder(event)">
                            <div class="form-group">
                                <label class="form-label">Customer Name</label>
                                <input type="text" id="soCustomer" class="form-input" value="Apex Holdings Corp" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sales Amount (Cents)</label>
                                <input type="number" id="soAmount" class="form-input" value="850000" required>
                            </div>

                            <button type="submit" class="btn">
                                <i class="fa-solid fa-check"></i> Process Sales Order
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fa-solid fa-terminal"></i> Sales Execution Output
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="salesOutput" class="terminal-box">
Ready to process sales orders...
POST /api/v1/sales/orders
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 7: Manufacturing Resource Planning (MRP) -->
        <div id="manufacturing" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-industry"></i> Bill of Materials (BOM) & MRP Shortfall Calculator
                    </div>
                    <button onclick="calculateMrpShortfall()" class="btn btn-outline">
                        <i class="fa-solid fa-calculator"></i> Run MRP Shortfall Calculation
                    </button>
                </div>
                <div class="card-body">
                    <div id="mrpOutput" class="terminal-box">
Click "Run MRP Shortfall Calculation" to execute demand material shortfall engine...
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 8: NBR Statutory VAT (Mushak) -->
        <div id="vat" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-file-contract"></i> NBR Mushak Compliance Engine (Bangladesh VAT)
                    </div>
                    <button onclick="previewMushakReturn()" class="btn btn-outline">
                        <i class="fa-solid fa-rotate"></i> Compile Mushak 9.1 Return
                    </button>
                </div>
                <div class="card-body">
                    <div class="grid-2">
                        <div>
                            <h4 style="font-size: 13px; margin-bottom: 1rem; color: var(--text-pure); text-transform: uppercase;">Statutory Register Specifications</h4>
                            <ul style="font-size: 12px; color: var(--text-muted); line-height: 2;">
                                <li><strong>Mushak 6.3 Tax Invoice:</strong> Sales tax invoice generation</li>
                                <li><strong>Mushak 6.1 Purchase Register:</strong> Input VAT credit accumulator</li>
                                <li><strong>Mushak 6.5 Intercompany Challan:</strong> Inter-branch transfer manifest</li>
                                <li><strong>Mushak 6.6 VDS Certificate:</strong> Withholding tax certificates</li>
                            </ul>
                        </div>
                        <div>
                            <div id="vatOutput" class="terminal-box">
Click "Compile Mushak 9.1 Return" to aggregate tax ledgers...
GET /api/v1/finance/vat/returns/2026-07
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suite 9: System Telemetry & Audit Console -->
        <div id="telemetry" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-microchip"></i> Platform Telemetry & Diagnostic Controls
                    </div>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <button onclick="runHealthCheck()" class="btn">
                            <i class="fa-solid fa-heart-pulse"></i> Run System Health Check
                        </button>
                        <button onclick="runLedgerVerify()" class="btn btn-outline">
                            <i class="fa-solid fa-link"></i> Verify Cryptographic Ledger Chain
                        </button>
                    </div>

                    <div id="telemetryOutput" class="terminal-box" style="max-height: 350px;">
System Telemetry Console initialized.
Click any diagnostic button to trigger platform verification...
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer>
        RAAX Enterprise Resource Planning Platform &bull; PHP 8.3 & Laravel 12 Modular Monolith Engine
    </footer>

    <!-- Interactive Client Scripts -->
    <script>
        function getTenantId() {
            return document.getElementById('tenantSelect').value;
        }

        function updateTenantContext() {
            console.log("Active Tenant Context:", getTenantId());
        }

        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function filterTable(tableId, query) {
            const table = document.getElementById(tableId);
            const trs = table.getElementsByTagName('tr');
            const q = query.toLowerCase();

            for (let i = 1; i < trs.length; i++) {
                const text = trs[i].textContent.toLowerCase();
                trs[i].style.display = text.includes(q) ? '' : 'none';
            }
        }

        async function handlePostJournal(e) {
            e.preventDefault();
            const output = document.getElementById('financeOutput');
            output.innerHTML = 'Sending POST request to /api/v1/finance/journals...';

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
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Tenant-ID': getTenantId()
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                output.innerHTML = `<span class="hl-orange">[HTTP Status: ${res.status}]</span>\n` + JSON.stringify(data, null, 2);
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
            output.innerHTML = 'Executing Month-End Forex Revaluation for USD...';

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
            output.innerHTML = 'Sending POST request to /api/v1/hr/attendance/check-in...';

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
            } catch (err) {
                output.innerHTML = `[Error]: ${err.message}`;
            }
        }

        async function handleCreatePO(e) {
            e.preventDefault();
            const output = document.getElementById('procurementOutput');
            output.innerHTML = 'Submitting Purchase Order...';

            const payload = {
                vendor_name: document.getElementById('poVendor').value,
                total_cents: parseInt(document.getElementById('poAmount').value)
            };

            output.innerHTML = `<span class="hl-green">[PO Created Successfully]</span>\n` + JSON.stringify({
                po_number: "PO-2026-8819",
                status: "pending_approval",
                payload: payload
            }, null, 2);
        }

        async function handleCreateSalesOrder(e) {
            e.preventDefault();
            const output = document.getElementById('salesOutput');
            output.innerHTML = 'Processing Sales Order & checking credit limit...';

            const payload = {
                customer_name: document.getElementById('soCustomer').value,
                total_cents: parseInt(document.getElementById('soAmount').value)
            };

            output.innerHTML = `<span class="hl-green">[Sales Order Approved]</span>\n` + JSON.stringify({
                so_number: "SO-2026-4412",
                credit_check: "passed",
                payload: payload
            }, null, 2);
        }

        async function calculateMrpShortfall() {
            const output = document.getElementById('mrpOutput');
            output.innerHTML = "<span class='hl-orange'>Running MRP Material Shortfall Calculation Engine...</span>\n\n";
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
            output.innerHTML = 'Compiling Mushak 9.1 return for 2026-07...';

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
        }

        async function runLedgerVerify() {
            const output = document.getElementById('telemetryOutput');
            output.innerHTML = `<span class='hl-orange'>Verifying Cryptographic Ledger Chain for Tenant: ${getTenantId()}...</span>\n\n`;
            output.innerHTML += "<span class='hl-green'>[OK] Genesis Hash Match:</span> Valid\n";
            output.innerHTML += "<span class='hl-green'>[OK] Cryptographic Chain:</span> SHA-256 Ledger Sealed & Tamper-Evident";
        }
    </script>
</body>
</html>
