<!-- Module View: Procurement & POs -->
<div id="view-procurement" class="view-panel">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Purchase Orders Directory & Price Tolerance Checks</div>
            <button class="btn btn-sm" onclick="openCreateModal('po')"><i class="fa-solid fa-plus"></i> + New PO Record</button>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="poTable">
                <thead><tr><th>PO Number</th><th>Vendor Name</th><th>Total Amount</th><th>Status</th><th>Print Voucher</th></tr></thead>
                <tbody id="poTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
