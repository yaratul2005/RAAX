<!-- Module View: Procurement & POs -->
<div id="view-procurement" class="view-panel">
    <!-- 3-Way Matching Inspector & Price Tolerance Tool -->
    <div class="stagger-1" style="background:var(--card-bg); border:1px solid var(--border-subtle); border-radius:6px; padding:0.85rem 1rem; margin-bottom:1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Procurement Control & 3-Way Matching Guard</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">PO #PO-2026-8819 vs GRN #GRN-4410 vs Supplier Invoice #INV-8819 — <span style="color:var(--status-green); font-weight:700;">MATCHED (0% Variance)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('threeWayMatchModal').classList.add('open')"><i class="fa-solid fa-magnifying-glass"></i> 3-Way Match Check</button>
            <button class="btn btn-sm" onclick="openCreateModal('po')"><i class="fa-solid fa-plus"></i> + New PO Record</button>
        </div>
    </div>

    <div class="card stagger-2">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Purchase Orders Directory & Price Tolerance Checks</div>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="poTable">
                <thead><tr><th>PO Number</th><th>Vendor Name</th><th>Total Amount</th><th>Status</th><th>Print Voucher</th></tr></thead>
                <tbody id="poTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
