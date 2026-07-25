<!-- Module View: Audit Trail Console -->
<div id="view-audit" class="view-panel">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-history"></i> Append-Only Immutable System Audit Trail (JSON Before/After State Diff)</div>
            <button class="btn btn-outline btn-sm" onclick="showToast('Exporting Audit Trail log to CSV...')"><i class="fa-solid fa-download"></i> Export Audit Log</button>
        </div>
        <div class="card-body">
            <div class="terminal-box" style="height:280px;"><span class="hl-orange">[2026-07-25 12:45:10 UTC]</span> USER: A. Rahman (ID: e1000000) | ACTION: JournalEntry.Posted | IP: 127.0.0.1
  BEFORE: {"status": "draft", "amount_cents": 4500000}
  AFTER:  {"status": "posted", "amount_cents": 4500000, "hash": "31af3d709ad29613..."}

<span class="hl-green">[2026-07-25 12:30:22 UTC]</span> USER: M. Hossain (ID: e1000002) | ACTION: SalesOrder.Confirmed | IP: 127.0.0.1
  BEFORE: {"status": "draft", "order_number": "SO-2026-4412"}
  AFTER:  {"status": "confirmed", "order_number": "SO-2026-4412", "mushak_6_3": "GEN-6.3-9912"}

<span class="hl-orange">[2026-07-25 12:15:04 UTC]</span> SYSTEM: ReorderTrigger | ACTION: Inventory.LowStock | IP: INTERNAL
  BEFORE: {"item_sku": "SKU-FASTENER-A", "qty": 310}
  AFTER:  {"item_sku": "SKU-FASTENER-A", "qty": 150, "reorder_status": "TRIGGERED"}</div>
        </div>
    </div>
</div>
