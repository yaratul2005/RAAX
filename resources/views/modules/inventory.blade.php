<!-- Module View: Inventory & Warehousing -->
<div id="view-inventory" class="view-panel">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-boxes-packing"></i> FIFO Stock Valuation & Multi-Bin Location Directory</div>
            <button class="btn btn-sm" onclick="showToast('Stock adjustment write-off modal ready.')"><i class="fa-solid fa-sliders"></i> Stock Adjustment</button>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="inventoryTable">
                <thead><tr><th>Item SKU</th><th>Bin Label</th><th>Original Qty</th><th>Remaining Qty</th><th>Unit Cost</th></tr></thead>
                <tbody id="inventoryTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
