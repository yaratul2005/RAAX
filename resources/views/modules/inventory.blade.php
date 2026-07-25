<!-- Module View: Inventory & Warehousing -->
<div id="view-inventory" class="view-panel">
    <!-- Stock Transfer Wizard & ZPL Label Generator Bar -->
    <div class="stagger-1" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Inter-Bin Stock Transfer Wizard</div>
                <div style="font-size:12px; color:var(--text-pure); margin-top:2px;">Move stock from BIN-MAIN-A1 to BIN-MAIN-B4</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('stockTransferModal').classList.add('open')"><i class="fa-solid fa-arrows-left-right"></i> Transfer Stock</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Zebra ZPL Label Generator</div>
                <div style="font-size:12px; color:var(--text-pure); margin-top:2px;">Print 2"x1" Thermal Barcode Bin Label</div>
            </div>
            <button class="btn btn-sm" onclick="document.getElementById('zplModal').classList.add('open')"><i class="fa-solid fa-barcode"></i> View ZPL Code</button>
        </div>
    </div>

    <div class="card stagger-2">
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
