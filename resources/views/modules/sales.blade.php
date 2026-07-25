<!-- Module View: Sales & Invoicing -->
<div id="view-sales" class="view-panel">
    <!-- Customer Credit Limit & Quotation Converter Bar -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Customer Credit Risk Checker</div>
                <div style="font-size:13px; font-weight:700; color:var(--text-pure); margin-top:2px;">Apex Holdings Corp — Outstanding: <span class="mono" style="color:var(--status-green);">BDT 850,000</span> / Approved Limit: <span class="mono">BDT 2,000,000</span></div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="showToast('Customer Credit Limit verified OK!')"><i class="fa-solid fa-shield-halved"></i> Audit Credit Risk</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Quotation Converter</div>
                <div style="font-size:11.5px; color:var(--text-muted);">Convert Quotation to Sales Order</div>
            </div>
            <button class="btn btn-sm" onclick="showToast('Quotation QTN-9901 converted to Sales Order SO-2026-4413 cleanly!')"><i class="fa-solid fa-arrows-rotate"></i> Convert</button>
        </div>
    </div>

    <div class="card">
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
