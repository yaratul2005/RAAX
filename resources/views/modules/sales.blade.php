<!-- Module View: Sales & Invoicing -->
<div id="view-sales" class="view-panel">
    <!-- Customer Credit Limit & Quotation Converter Bar -->
    <div class="stagger-1" style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Customer Credit Risk Checker</div>
                <div style="font-size:13px; font-weight:700; color:var(--text-pure); margin-top:2px;">Apex Holdings Corp — Outstanding: <span class="mono" style="color:var(--status-green);">BDT 850,000</span> / Approved Limit: <span class="mono">BDT 2,000,000</span></div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('creditRiskModal').classList.add('open')"><i class="fa-solid fa-shield-halved"></i> Audit Credit Risk</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">NBR Mushak 6.3 Invoice</div>
                <div style="font-size:11.5px; color:var(--text-muted);">Statutory Tax Invoice Viewer</div>
            </div>
            <button class="btn btn-sm" onclick="printDocument('SO-2026-4412', 'mushak63')"><i class="fa-solid fa-print"></i> Print Mushak 6.3</button>
        </div>
    </div>

    <div class="card stagger-2">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-receipt"></i> Commercial Sales Orders & Invoicing (Double-Click to Edit, Right-Click for Context Menu)</div>
            <button class="btn btn-sm" onclick="openCreateModal('so')"><i class="fa-solid fa-plus"></i> + New Sales Order</button>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="salesTable">
                <thead><tr><th>Order ID</th><th>Customer Name</th><th>Subtotal</th><th>Grand Total</th><th>Status</th><th>Mushak 6.3 Invoice</th></tr></thead>
                <tbody id="salesTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
