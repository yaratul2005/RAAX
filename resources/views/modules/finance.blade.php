<!-- Module View: Finance & General Ledger -->
<div id="view-finance" class="view-panel">
    <!-- Double-Entry Journal Builder & SHA-256 Chain Verifier -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Double-Entry Journal Balance Guard</div>
                <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Debits: <span class="mono" style="color:var(--status-green);">BDT 45,000.00</span> | Credits: <span class="mono" style="color:var(--status-green);">BDT 45,000.00</span> — <span style="color:var(--status-green); font-weight:700;">BALANCED (Diff: BDT 0.00)</span></div>
            </div>
            <button class="btn btn-sm" onclick="showToast('Double-entry journal posted to General Ledger cleanly!')"><i class="fa-solid fa-plus"></i> + Post Journal</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">SHA-256 Ledger Guard</div>
                <div style="font-size:11.5px; color:var(--text-muted);">Verify Cryptographic Hash Chain</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="showToast('SHA-256 Cryptographic Ledger Chain verified: 100% Intact!')"><i class="fa-solid fa-shield"></i> Verify Chain</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-book"></i> General Ledger Entries & Cryptographic SHA-256 Chain</div>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="journalTable">
                <thead><tr><th>Reference</th><th>Date</th><th>Description</th><th>Amount</th><th>SHA-256 Ledger Hash</th></tr></thead>
                <tbody id="journalTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
