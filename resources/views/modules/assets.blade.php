<!-- Module View: Fixed Assets Register -->
<div id="view-assets" class="view-panel">
    <!-- Valuation & Depreciation Engine Toolbar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Fixed Assets Register & Depreciation Engine</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Total Book Valuation: <span class="mono" style="color:var(--status-green);">BDT 84,100,000</span> | Active Assets: <span class="mono">142 Items</span> | Method: <span style="color:var(--orange-brand); font-weight:700;">SLM & DDB Dual Schedule</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('assetDisposalModal').classList.add('open')"><i class="fa-solid fa-calculator"></i> Asset Disposal Tool</button>
            <button class="btn btn-sm" onclick="document.getElementById('depreciationModal').classList.add('open')"><i class="fa-solid fa-sync"></i> Run Depreciation Schedule</button>
        </div>
    </div>

    <!-- 5-Year Depreciation Curve Visualizer -->
    <div class="card stagger-2" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-chart-line"></i> 5-Year Asset Depreciation Schedule (Straight-Line vs Double-Declining Balance)</div>
        </div>
        <div class="card-body">
            <div class="kpi-grid" style="margin-bottom:1rem;">
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Initial Acquisition Cost</div>
                    <div style="font-size:18px; font-weight:700; color:var(--text-pure);">BDT 112.5M</div>
                    <div style="font-size:10px; color:var(--text-muted);">Historical Cost Basis</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Accumulated Depreciation</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-red);">BDT 28.4M</div>
                    <div style="font-size:10px; color:var(--status-red);">Contra Asset Ledger</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Net Book Value (NBV)</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-green);">BDT 84.1M</div>
                    <div style="font-size:10px; color:var(--status-green);">Balance Sheet Carrying Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed Assets Directory Table -->
    <div class="card stagger-3">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-building-columns"></i> Corporate Fixed Asset Master Registry</div>
            <button class="btn btn-sm" onclick="openRecordEditor('AST-NEW', 'Fixed Assets')"><i class="fa-solid fa-plus"></i> + Add Fixed Asset</button>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead><tr><th>Asset Code</th><th>Description</th><th>Acquisition Date</th><th>Cost Basis</th><th>Accumulated Dep.</th><th>Net Book Value</th></tr></thead>
                <tbody>
                    <tr ondblclick="openRecordEditor('AST-VEH-004', 'Fixed Assets')">
                        <td class="mono">AST-VEH-004</td>
                        <td>Commercial Delivery Truck (Toyota Hino)</td>
                        <td>2024-01-15</td>
                        <td class="mono">BDT 4,500,000</td>
                        <td class="mono" style="color:var(--status-red);">BDT 900,000</td>
                        <td class="mono" style="color:var(--status-green); font-weight:700;">BDT 3,600,000</td>
                    </tr>
                    <tr ondblclick="openRecordEditor('AST-MAC-012', 'Fixed Assets')">
                        <td class="mono">AST-MAC-012</td>
                        <td>CNC Hydraulic Press Machine (Germany)</td>
                        <td>2023-06-10</td>
                        <td class="mono">BDT 12,500,000</td>
                        <td class="mono" style="color:var(--status-red);">BDT 3,750,000</td>
                        <td class="mono" style="color:var(--status-green); font-weight:700;">BDT 8,750,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
