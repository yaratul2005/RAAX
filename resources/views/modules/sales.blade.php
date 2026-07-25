<!-- Module View: Sales & Invoicing -->
<div id="view-sales" class="view-panel">
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
