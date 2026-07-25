<!-- Module View: Fixed Assets & Depreciation -->
<div id="view-assets" class="view-panel">
    <!-- Depreciation Method Switcher & Asset Disposal Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Fixed Asset Depreciation Engine</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Total Book Valuation: <span class="mono" style="color:var(--status-blue);">BDT 84,100,000</span> | Active Method: <span class="mono">Straight-Line (10%) & DDB (20%)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="showToast('Asset AST-VEH-004 disposal registered cleanly!')"><i class="fa-solid fa-dumpster"></i> Asset Disposal</button>
            <button class="btn btn-sm" onclick="showToast('Depreciation schedules posted to General Ledger cleanly!')"><i class="fa-solid fa-rotate"></i> Recalculate Book Values</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-building-columns"></i> Fixed Asset Register & Depreciation Engine Logs</div>
        </div>
        <div class="card-body">
            <div class="terminal-box"><span class="hl-orange">[Fixed Asset Depreciation Engine Active]</span>
AST-COMP-001: Original Cost BDT 1,200,000 -> Straight Line Depr (10% p.a.): BDT 120,000 -> Book Value: BDT 1,080,000
AST-VEH-004: Original Cost BDT 4,500,000 -> Double Declining Depr (20% p.a.): BDT 900,000 -> Book Value: BDT 3,600,000</div>
        </div>
    </div>
</div>
