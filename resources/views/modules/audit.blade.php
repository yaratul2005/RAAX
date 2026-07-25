<!-- Module View: Audit Trail Console -->
<div id="view-audit" class="view-panel">
    <!-- JSON Before/After Diff Inspector Toolbar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Append-Only Immutable System Audit Console</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Audit Registry: <span class="mono" style="color:var(--status-green);">100% Append-Only Secured</span> | SHA-256 Tamper Protection Active</div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('jsonDiffModal').classList.add('open')"><i class="fa-solid fa-code-compare"></i> Inspect JSON Diff</button>
            <button class="btn btn-sm" onclick="showToast('Exporting complete Audit Trail log to CSV format...')"><i class="fa-solid fa-download"></i> Export Audit Log</button>
        </div>
    </div>

    <div class="card stagger-2">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-history"></i> Append-Only Immutable System Audit Trail (Click Row to Inspect JSON Diff)</div>
        </div>
        <div class="card-body">
            <div class="terminal-box" style="height:280px; cursor:pointer;" onclick="document.getElementById('jsonDiffModal').classList.add('open')"><span class="hl-orange">[2026-07-25 12:45:10 UTC]</span> USER: A. Rahman (ID: e1000000) | ACTION: JournalEntry.Posted | IP: 127.0.0.1
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
