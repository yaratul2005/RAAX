<!-- Module View: NBR Bangladesh Statutory VAT -->
<div id="view-vat" class="view-panel">
    <!-- Statutory VAT Return & Certificate Action Toolbar -->
    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">NBR Bangladesh Statutory VAT Engine</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">BIN: <span class="mono">1899201928301</span> | Current Period: <span class="mono">2026-07</span> | Net Tax Payable: <span class="mono" style="color:var(--orange-brand);">BDT 65,869.57</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="showToast('Issued Mushak 6.6 VDS Withholding Certificate VDS-2026-012 cleanly!')"><i class="fa-solid fa-file-shield"></i> Issue Mushak 6.6 VDS</button>
            <button class="btn btn-sm" onclick="showToast('Exporting Mushak 9.1 Monthly Return XML/PDF...')"><i class="fa-solid fa-download"></i> Export Mushak 9.1 Return</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-file-contract"></i> NBR Bangladesh Statutory VAT Compliance Suite Logs</div>
        </div>
        <div class="card-body">
            <div class="terminal-box"><span class="hl-orange">[Mushak Compliance Engine 2026-07]</span>
- Mushak 6.1 (Purchase Register): BDT 1,250,000 input tax credit claims verified
- Mushak 6.3 (Sales Tax Invoice): BDT 850,000 invoice dispatched (15% VAT: BDT 110,870)
- Mushak 6.6 (VDS Certificate): BDT 45,000 withholding tax certificate generated
- Mushak 9.1 (Monthly VAT Return): Net Payable BDT 65,869.57</div>
        </div>
    </div>
</div>
