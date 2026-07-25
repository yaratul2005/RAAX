<!-- Module View: Manufacturing Resource Planning (MRP) -->
<div id="view-manufacturing" class="view-panel">
    <!-- Work Orders & MRP Engine Toolbar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Manufacturing MRP & Shop Floor Routing Control</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Active Work Orders: <span class="mono" style="color:var(--status-green);">12 Orders</span> | BOM Assemblies: <span class="mono">48 Products</span> | MRP Shortfall Runner: <span style="color:var(--status-green); font-weight:700;">OK (Zero Material Shortfalls)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('mrpRunnerModal').classList.add('open')"><i class="fa-solid fa-calculator"></i> Run MRP Shortfall Runner</button>
            <button class="btn btn-sm" onclick="document.getElementById('workOrderModal').classList.add('open')"><i class="fa-solid fa-plus"></i> + Create Work Order</button>
        </div>
    </div>

    <!-- Bill of Materials (BOM) Tree Explosion Explorer Card -->
    <div class="card stagger-2" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-sitemap"></i> Bill of Materials (BOM) Tree Explosion Explorer</div>
            <button class="btn btn-outline btn-sm" onclick="showToast('BOM Tree expanded to raw material level')"><i class="fa-solid fa-expand"></i> Expand All Nodes</button>
        </div>
        <div class="card-body">
            <div class="terminal-box" style="height:170px;"><span class="hl-orange">[FINISHED GOOD ASSEMBLY] FG-STEEL-STRUCTURE-01 (Structural Steel Building Frame)</span>
├── [SUB-ASSEMBLY 1] SUB-STEEL-BEAM-A (Qty: 4 units)
│   ├── [RAW MATERIAL] SKU-RAW-STEEL (Heavy Steel Plates - Qty: 120 kg)
│   └── [PROCESS ROUTING] Cut & Weld Step (Work Center: WC-FABRICATION)
└── [SUB-ASSEMBLY 2] SUB-FASTENER-KIT-B (Qty: 1 kit)
    ├── [RAW MATERIAL] SKU-FASTENER-A (Heavy Duty Fastener - Qty: 40 units)
    └── [PROCESS ROUTING] Torque & Quality Check Step (Work Center: WC-ASSEMBLY)</div>
        </div>
    </div>

    <!-- Active Production Work Orders Table -->
    <div class="card stagger-3">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-industry"></i> Shop Floor Active Production Work Orders</div>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead><tr><th>Work Order ID</th><th>BOM Assembly SKU</th><th>Target Qty</th><th>Work Center</th><th>Routing Step</th><th>Status</th></tr></thead>
                <tbody>
                    <tr>
                        <td class="mono">WO-2026-0881</td>
                        <td class="mono">FG-STEEL-STRUCTURE-01</td>
                        <td>25 Units</td>
                        <td>WC-FABRICATION</td>
                        <td>10-Welding & Fitting</td>
                        <td><span class="status-chip active">IN_PROGRESS</span></td>
                    </tr>
                    <tr>
                        <td class="mono">WO-2026-0882</td>
                        <td class="mono">FG-FASTENER-BOX-HD</td>
                        <td>500 Boxes</td>
                        <td>WC-ASSEMBLY</td>
                        <td>20-Packaging & Seal</td>
                        <td><span class="status-chip active">IN_PROGRESS</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
