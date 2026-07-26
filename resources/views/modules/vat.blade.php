<!-- Module View: NBR Bangladesh Statutory VAT -->
<div id="view-vat" class="view-panel">
    <!-- Statutory VAT Return & Certificate Action Toolbar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <img src="/govt-logo.png" style="height:42px; width:auto; border-radius:4px; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.5));" alt="NBR Bangladesh Government Seal">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">NBR Bangladesh Statutory VAT Engine</div>
                <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">BIN: <span class="mono">1899201928301</span> | Current Period: <span class="mono">2026-07</span> | Net Tax Payable: <span class="mono" style="color:var(--orange-brand); font-weight:700;">BDT 65,869.57</span></div>
            </div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('vdsCertificateModal').classList.add('open')"><i class="fa-solid fa-file-shield"></i> Issue Mushak 6.6 VDS</button>
            <button class="btn btn-sm" onclick="printDocument('VAT-2026-07', 'mushak63')"><i class="fa-solid fa-download"></i> Export Mushak 9.1 Return</button>
        </div>
    </div>

    <!-- Statutory VAT Metrics Scorecard -->
    <div class="kpi-grid stagger-2" style="margin-bottom:1.25rem;">
        <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
            <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Mushak 6.3 Taxable Sales</div>
            <div style="font-size:18px; font-weight:700; color:var(--text-pure);">BDT 739,130.43</div>
            <div style="font-size:10px; color:var(--status-green);">15% VAT Collected: BDT 110,869.57</div>
        </div>
        <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
            <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Mushak 6.1 Input Tax Credit</div>
            <div style="font-size:18px; font-weight:700; color:var(--status-green);">BDT 45,000.00</div>
            <div style="font-size:10px; color:var(--status-green);">Rebate Claim Approved</div>
        </div>
        <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
            <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Mushak 6.6 VDS Withheld</div>
            <div style="font-size:18px; font-weight:700; color:var(--orange-brand);">BDT 6,750.00</div>
            <div style="font-size:10px; color:var(--text-muted);">Supplier VDS Certificate</div>
        </div>
        <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
            <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Net Treasury Payable</div>
            <div style="font-size:18px; font-weight:700; color:var(--orange-brand);">BDT 65,869.57</div>
            <div style="font-size:10px; color:var(--status-green);">Due Date: 15th Aug 2026</div>
        </div>
    </div>

    <!-- Mushak 9.1 Monthly Return Tabbed Compiler Card -->
    <div class="card stagger-3">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-file-contract"></i> NBR Bangladesh Statutory VAT Compliance Suite & Return Logs</div>
            <button class="btn btn-outline btn-sm" onclick="printDocument('MUSHAK-9.1-JUL26', 'mushak63')"><i class="fa-solid fa-print"></i> Print Official Mushak 9.1</button>
        </div>
        <div class="card-body">
            <div class="terminal-box" style="height:180px;"><span class="hl-orange">[NBR Bangladesh Statutory VAT Engine 2026-07 Compliance Registry]</span>
- Mushak 6.1 (Purchase Register Log): BDT 1,250,000.00 raw steel inventory purchases verified. Input tax credit rebate claims verified cleanly.
- Mushak 6.3 (Sales Tax Invoice Log): Invoice #SO-2026-4412 dispatched (Sales: BDT 739,130.43, 15% VAT: BDT 110,869.57).
- Mushak 6.6 (VDS Certificate Register): Certificate #VDS-2026-012 issued for BDT 45,000.00 supplier invoice.
- Mushak 9.1 (Monthly Tax Return Compiler): Parts 1-12 compiled cleanly. Net Treasury Deposit Liability: BDT 65,869.57.</div>
        </div>
    </div>
</div>
