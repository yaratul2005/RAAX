<!-- Module View: Role Dashboard -->
<div id="view-dashboard" class="view-panel active">
    <!-- Quick Action Bar -->
    <div class="stagger-1" style="display:flex; gap:8px; margin-bottom:1.25rem; background:var(--card-bg); padding:0.75rem 1rem; border:1px solid var(--border-subtle); border-radius:6px; align-items:center; flex-wrap:wrap;">
        <span style="font-size:11px; font-weight:700; color:var(--text-dim); text-transform:uppercase; margin-right:4px;"><i class="fa-solid fa-bolt" style="color:var(--orange-brand);"></i> Executive Quick Actions:</span>
        <button class="btn btn-sm" onclick="openCreateModal('so')"><i class="fa-solid fa-plus"></i> + Sales Order</button>
        <button class="btn btn-outline btn-sm" onclick="openCreateModal('po')"><i class="fa-solid fa-cart-plus"></i> + Purchase Order</button>
        <button class="btn btn-outline btn-sm" onclick="document.getElementById('journalModal').classList.add('open')"><i class="fa-solid fa-book"></i> + Post Journal</button>
        <button class="btn btn-outline btn-sm" onclick="document.getElementById('stockTransferModal').classList.add('open')"><i class="fa-solid fa-boxes-packing"></i> Stock Transfer</button>
    </div>

    <!-- KPI Headline Grid with Trends -->
    <div class="kpi-grid stagger-2">
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

    <!-- Financial Ratios & Performance Sparkline Card -->
    <div class="card stagger-3" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-chart-bar"></i> Financial Ratios & 12-Month Performance Trend</div>
            <span style="font-size:11px; color:var(--text-dim);">FY 2026-2027 Audited Metrics</span>
        </div>
        <div class="card-body">
            <div class="kpi-grid" style="margin-bottom:1rem;">
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Current Ratio</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-green);">2.48x</div>
                    <div style="font-size:10px; color:var(--text-muted);">Target: &gt; 1.5x</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Quick Ratio</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-green);">1.82x</div>
                    <div style="font-size:10px; color:var(--text-muted);">Target: &gt; 1.0x</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Debt-to-Equity</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-green);">0.42</div>
                    <div style="font-size:10px; color:var(--text-muted);">Target: &lt; 0.8</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Operating Margin</div>
                    <div style="font-size:18px; font-weight:700; color:var(--orange-brand);">18.5%</div>
                    <div style="font-size:10px; color:var(--text-muted);">Target: &gt; 15.0%</div>
                </div>
            </div>

            <!-- SVG Revenue vs Expense Sparkline Bar Chart -->
            <div style="display:flex; align-items:flex-end; gap:8px; height:80px; padding-top:10px; border-top:1px solid var(--border-subtle);">
                <div style="flex:1; background:var(--orange-brand); height:45%; border-radius:3px 3px 0 0;" title="Jan: BDT 10.2M"></div>
                <div style="flex:1; background:var(--orange-brand); height:55%; border-radius:3px 3px 0 0;" title="Feb: BDT 11.8M"></div>
                <div style="flex:1; background:var(--orange-brand); height:60%; border-radius:3px 3px 0 0;" title="Mar: BDT 12.5M"></div>
                <div style="flex:1; background:var(--orange-brand); height:70%; border-radius:3px 3px 0 0;" title="Apr: BDT 14.1M"></div>
                <div style="flex:1; background:var(--orange-brand); height:65%; border-radius:3px 3px 0 0;" title="May: BDT 13.8M"></div>
                <div style="flex:1; background:var(--orange-brand); height:85%; border-radius:3px 3px 0 0;" title="Jun: BDT 18.2M"></div>
                <div style="flex:1; background:var(--orange-brand); height:100%; border-radius:3px 3px 0 0;" title="Jul: BDT 21.5M"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:9.5px; color:var(--text-dim); margin-top:4px;">
                <span>JAN</span><span>FEB</span><span>MAR</span><span>APR</span><span>MAY</span><span>JUN</span><span>JUL (CURRENT)</span>
            </div>
        </div>
    </div>

    <!-- High-Density Commercial Content Layout -->
    <div class="grid-2 stagger-3">
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
