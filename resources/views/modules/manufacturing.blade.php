<!-- Module View: Manufacturing & MRP -->
<div id="view-manufacturing" class="view-panel">
    <!-- BOM Explosion Tree & JIT MRP Runner Bar -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Bill of Materials (BOM) & JIT MRP Engine</div>
                <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Work Order WO-2026-881 — Material Shortfall: <span class="mono" style="color:var(--status-red);">SKU-FASTENER-A (350 units)</span></div>
            </div>
            <button class="btn btn-sm" onclick="showToast('MRP Material Shortfall calculation finished cleanly!')"><i class="fa-solid fa-play"></i> Run MRP Engine</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">BOM Explosion Tree</div>
                <div style="font-size:11.5px; color:var(--text-muted);">Expand Bill of Materials</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="showToast('BOM Tree for Assembly FG-STEEL-STRUCTURE expanded!')"><i class="fa-solid fa-sitemap"></i> View BOM Tree</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-industry"></i> JIT Manufacturing MRP Material Deficiency Output Logs</div>
        </div>
        <div class="card-body">
            <div class="terminal-box"><span class="hl-orange">[MRP Shortfall Engine Output]</span>
Work Order WO-2026-881 Requirements:
- SKU-RAW-STEEL: Required 200 | Stock 1,200 | Shortfall: 0 (Stock Available)
- SKU-FASTENER-A: Required 500 | Stock 150 | Shortfall: 350 (Reorder Trigger Dispatched)</div>
        </div>
    </div>
</div>
